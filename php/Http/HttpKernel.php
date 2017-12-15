<?php
/**
 * Created by Chen.
 * Date: 2016/5/19
 * Time: 9:58
 */
namespace Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

abstract class HttpKernel
{
    protected $request;
    //validate
    protected $validateClose = false;
    protected $validateKeys = [];
    protected $rules = [];
    protected $validateMessage = [];
    //
    protected $user;
    protected $input;
    protected $output;

    public function __construct()
    {
        db()->enableQueryLog();
        $this->request = request();
    }

    public function pagination()
    {
        Paginator::currentPageResolver(function ($pageName = 'page') {
            $page = $this->request->input($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
                return $page;
            }

            return 1;
        });
    }

    public function input($key = null, $default = null)
    {
        return $this->request->input($key, $default);
    }

    public function filter()
    {
        $request = $this->request;
        $input = [];
        foreach ($this->validateKeys as $key => $value) {
            if ($request->hasFile($value)) {
                $input[$value] = $request->file($value);
                continue;
            }
            if (is_numeric($key)) {
                $inputValue = $request->input($value);
                if (is_array($inputValue)) {
                    array_walk_recursive($inputValue, function (&$v) {
                        $v = trim($v);
                    });
                } else {
                    $inputValue = trim($inputValue);
                }
                $input[$value] = $inputValue;
            } else {
                $inputValue = $request->input($key, $value);
                if (is_array($inputValue)) {
                    array_walk_recursive($inputValue, function (&$v) {
                        $v = trim($v);
                    });
                } else {
                    $inputValue = trim($inputValue);
                }
                $input[$key] = $inputValue;
            }
        }
        $this->input = $input;
        $this->request = $request->duplicate($input);
    }

    public function validate()
    {
        $validator = validator($this->request->all(), $this->rules, $this->validateMessage);
        if ($validator->fails()) {
            $data = [
                'return' => 'false',
                'msg' => $validator->errors()->all()[0]
            ];
            throw new ValidationException($validator, $data);
        }
    }

    public function authenticate()
    {
    }

    public function authorise()
    {
    }

    /**
     *公共继承
     */
    public function Common()
    {
    }

    abstract public function handle();

    public function catchException(\Exception $e)
    {
        $data['return'] = 'false';
        $data['msg'] = $e->getMessage();
        $debug = [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'sql' => $this->deBug(),
            'query' => $this->input
        ];
        $deMsg = array_merge($data, $debug);
        if (config('app.debug') && config('encrypt.enable')) $data = $deMsg;
        logger()->info(httpUrl(), $deMsg);
        $this->output = $data;
    }

    public function deBug()
    {
        $sqlLog = db()->getQueryLog();
        db()->flushQueryLog();
        return $sqlLog;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respond()
    {
        if (is_array($this->output)) {
            if (config('app.debug') && config('encrypt.enable')) $this->output['sql'] = $this->deBug();
            if (preg_match('/^([a-zA-Z_\.\s]+)$/', @$this->output['msg']))
                $this->output['msg'] = trans('exception.' . $this->output['msg']);
            try {
                $json = new JsonResponse($this->output, 200, [], JSON_UNESCAPED_UNICODE);
//            if (isset($this->input['callback']))
//        $json->setCallback('chasdas');
                return $json->getContent();
            } catch (\Exception $e) {
                return print_r($this->output);
            }
        } else return $this->output;
    }

    public function run()
    {
        try {
            $this->pagination();
            $this->authenticate();
            $this->authorise();
            if (!$this->validateClose) {
                $this->filter();
                $this->validate();
            }
            $this->Common();
            $this->handle();
        } catch (ValidationException $e) {
            $this->output = $e->getResponse();
        } catch (\Exception $e) {
            $this->catchException($e);
        }
        return $this->respond();
    }
}
