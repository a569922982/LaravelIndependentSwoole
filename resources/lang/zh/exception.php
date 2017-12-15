<?php
/**
 * Created by Chen.
 * Date: 2016/6/18
 * Time: 10:31
 */
return [
    //Admin
    'Admin.authPassword.error' => '密码错误',
    'auth.no.User' => '请登录账号',
    'auth.no.admin' => '请重新登录',
    'Admin.auth.error' => '权限不够',

    //codeget
    'CodeGet.handle.Code.apiError' => '验证码获取错误',
    'CodeGet.handle.Code.apiSucceed' => '验证码获取成功',
    'The mobile field is required.' => '手机不能为空',
    'validation.mobile' => '手机格式不正确',

    'Code.handle.Code.CodeError' => '验证码错误',
    //userReg
    'UserRegister.handle.Register.alreadyUser' => '已存在账号',
    'UserRegister.handle.Register.UserRegisterSucceed' => '注册成功',
    'The user field is required.' => '',

    //UserForget
    'UserForget.handle.Forget.UserNot' => '用户不存在',
    'UserForget.handle.Forget.PasswordSucceed' => '密码修改成功',

    //UserLogin
    'UserLogin.handle.Login.UserNot' => '用户不存在',
    'UserLogin.handle.Login.Succeed' => '登录成功',
    'The sid field is required.' => '参数不正确',
    'User.authPassword.error' => '密码错误',

    //UserEdit
    'UserEdit.handle.Edit.UserNot' => '用户不存在',
    'UserEdit.handle.Edit.Succeed' => '设置成功',
    'UserEdit.handle.Edit.UserSaveErr' => '用户已存在',
    'UserAddressSave.handle.Edit.UserAddressSaveErr' => '用户地址已存在',
    'The address id must be an integer.' => '请选择地址',

    //Comment
    'CommentAdd.handle.Add.Succeed' => '发表成功',
    'CommentAdd.handle.AddZan.Succeed' => '点赞成功',
    'CommentAdd.handle.AddZan.Err' => '取消点赞',

    //WishAdd
    'WishAdd.handle.Add.Succeed' => '意向单提交成功',
    'WishAdd.handle.Add.False' => '意向单提交失败',


    'GetBasketball.handle.Add.Succeed' => '更新篮球成功',
    'GetBasketball.handle.Add.False' => '更新篮球失败',

    'AdvertisementEdit.handle.Edit.SaveErr' => '已存在广告名称',
    'GetFootball.handle.Add.False' => '更新足球失败',


    'ArticleClassEdit.handle.Edit.ShopCateSaveErr' => '分类已存在',
    'ArticleEdit.handle.Edit.ShopCateSaveErr' => '文章已存在',
    'ShopEdit.handle.Edit.ShopSaveErr' => '商品已存在',
    'ShopCateEdit.handle.Edit.ShopCateSaveErr' => '商品分类已存在',
    'ReflectEdit.handle.Edit.ShopCateSaveErr' => '备注已存在',


    'Order.handle.calculationWeight.Err' => '您购买本产品必须超过起步重量',


    'Order.handle.GroupSave.CountErr' => '拼团数量已满',
    'Order.handle.GroupSave.StatusErr' => '该商品拼团功能关闭',
    'Order.handle.ShopSave.StatusErr' => '该商品单买功能关闭',
    'Order.handle.orderGroupSave.StatusErr' => '该商品只能活动订购',

    'Order.handle.CouponSave.startErr' => '有优惠卷还没开始',
    'Order.handle.CouponSave.endErr' => '有优惠卷已过期',
    'Order.handle.CouponSave.statusErr' => '有优惠卷已失效',
    'Order.handle.CouponSave.Err' => '您购买该产品的优惠卷不足',


    'shopBuy.handle.buySave.Succeed' => '购买成功',
    'shopBuy.handle.giftSave.Succeed' => '购买成功，请您分享送礼',

    'Order.handle.GroupSave.Err' => '该拼团已完成',
    'Order.handle.shopBuy.Err' => '订单提交过快',
    'Order.handle.ShopSave.Err' => '不存在该商品',

    'Order.handle.priceSave.Err' => '金额不足以完成该订单，请充值',
    'Order.handle.GiftSend.Err' => '送礼订单不存在或已被领取',

    'Order.handle.stockSave.Err' => '所购买的商品库存不足',
    'Order.handle.offSave.Err' => '该商品已下架',
    'Group.handle.GroupOver.Err' => '该拼团已完成',
    'Order.handle.GroupSave.shopCountErr' => '该拼团商品不足',
    'Order.handle.OrderView.Err' => '不存在该订单',
    'Order.handle.OrderShare.Err' => '分享返现失败，该订单无返现优惠',
    'Order.handle.OrderShare.Succeed' => '分享返现成功',

    'Order.handle.OrderSend.Err' => '该订单还不能确定收货',
    'Order.handle.groupLimit.Err' => '您已经超过限购份数',
    'Coupon.handle.Coupon.Err' => '不存在该优惠卷',
    'Coupon.handle.CouponAdd.Err' => '您已经领取该优惠卷',
    'Order.handle.OrderDel.Err' => '不存在订单或不能删除',
    'Order.handle.giftView.Err' => '该订单已被领取',
    'UserGetCode.handle.getCode.Succeed' => '发送验证码成功',


    'UserWithdrawals.handle.price.Err' => '金额不足',
    'UserRecharge.handle.price.Err' => '金额不能小于0元',
    'Order.handle.new.Err' => '创建订单失败',
    'Order.handle.lineUp.Err' => '正在排队，请稍后再试',

    'User.handle.addPrice.remark' => '感谢您的支持，欢迎再次光临',

    'User.handle.addPrice.text' => '尊敬的用户，您好，管理员为您增加金额：:price元',
    'User.handle.reducePrice.text' => '尊敬的用户，您好，管理员为您减少金额：:price元',
    'User.handle.orderSn.text' => '订单号：:order_sn',

    'Order.handle.activityPrice.text' => '尊敬的用户，您好，活动拼团:title商品，返金额:price元',
    'Order.handle.shopWxPrice.text' => '尊敬的用户，您好，购买:title商品，使用微信支付:price元',
    'Order.handle.shopWxPrice.text.coupon' => '尊敬的用户，您好，购买:title商品，使用卷抵扣:coupon_price元，微信支付:price元',

    'Order.handle.shopPrice.text' => '尊敬的用户，您好，购买:title商品，使用余额支付:price元',
    'Order.handle.shopPrice.text.coupon' => '尊敬的用户，您好，购买:title商品，使用卷抵扣:coupon_price元，余额支付:price元',

    'Order.handle.shopCoupon.text' => '尊敬的用户，您好，购买:title商品，使用卷抵扣:coupon_price元',

    'UserRechargeNotifyUrl.handle.addPrice.text' => '尊敬的用户，您好，充值金额：:price元',
    'UserWithdrawals.handle.addPrice.Succeed' => '提现操作成功',
    'UserWithdrawals.handle.addPrice.text' => '尊敬的用户，您好，提现金额：:price元,提现金额预计在3个工作日内按原路返还。',

    'ReflectSave.handle.addPrice.text' => '尊敬的用户，您好，提现金额：:price元，失败，提现金额已返还您的账号。',

    'OrderCancel.handle.price.text' => '尊敬的用户，您好，:gift_text:title商品已取消，返现金额：:price元',

    'ShopGroupCron.handle.price.text' => '尊敬的用户，您好，拼团:title商品失败，返现：:price元',
    'ShopOrderCron.handle.price.text' => '尊敬的用户，您好，活动拼团:title商品失败，返现：:price元',
    'ShopGiftCron.handle.price.text' => '尊敬的用户，您好，送礼:title商品失败，返现：:price元',
    'ShopSave.handle.price.text' => '尊敬的用户，您好，您的:title商品发货成功，请注意接收。',
    'OrderGift.handle.price.text' => "尊敬的用户，您好，您的:title送礼订单已被接收",

    'OrderShare.handle.price.text' => '尊敬的用户，您好，分享:title商品，返现金额：:price元',

    'Order.handle.CouponBuy.Err' => '必须使用专属优惠券',
];