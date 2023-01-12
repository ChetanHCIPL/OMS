<div style="text-align:center">
    <img alt="<?php echo Config::get('settings.SITE_NAME.default'); ?>" title="site_logo" src="<?php echo (isset($site_logo)?$site_logo:""); ?>">
</div>
<p>Dear <?php echo (isset($name)?$name:""); ?>,</p>
<p>You are receiving this email because we received a password reset request for your account.</p><br/>
<div><a href="<?php echo route('sales.password.reset.token',['token' => $token]);?>" style="font-family:Avenir, Helvetica, sans-serif;color:#FFF;text-decoration:none;background-color:#3097D1;border-top:10px solid #3097D1;border-right:18px solid #3097D1;border-bottom:10px solid #3097D1;border-left:18px solid #3097D1;" class="button button-blue">Reset Password</a></div><br/><br/>
<div style="margin-bottom:4%;">
	<div style="line-height:2px">Regards,</div><br/>
	<div style="line-height:2px">{{ Config::get('settings.SITE_NAME.default')}}</div>
</div>