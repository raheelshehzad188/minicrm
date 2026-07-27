<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Account Activated</title></head>
<body style="font-family:Arial,sans-serif;background:#f1f5f9;padding:24px;">
  <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;">
    <h2 style="color:#0F766E;margin-top:0;">Account activated</h2>
    <p>Hi <?php echo html_escape($name); ?>,</p>
    <p>Your account at <strong><?php echo html_escape($org_name); ?></strong> has been activated. You can sign in again.</p>
    <p><a href="<?php echo html_escape($login_url); ?>" style="display:inline-block;background:#0F766E;color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;">Sign in</a></p>
  </div>
</body>
</html>
