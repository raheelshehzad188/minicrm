<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Welcome</title></head>
<body style="font-family:Arial,sans-serif;background:#f1f5f9;padding:24px;">
  <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;">
    <h2 style="color:#0F766E;margin-top:0;">Welcome to Mino CRM</h2>
    <p>Hi <?php echo html_escape($name); ?>,</p>
    <p>You've been added to <strong><?php echo html_escape($org_name); ?></strong>.</p>
    <p><strong>Email:</strong> <?php echo html_escape($email); ?><br>
       <strong>Temporary password:</strong> <?php echo html_escape($password); ?></p>
    <p><a href="<?php echo html_escape($login_url); ?>" style="display:inline-block;background:#0F766E;color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;">Sign in</a></p>
    <p style="color:#64748b;font-size:13px;">Please change your password after signing in.</p>
  </div>
</body>
</html>
