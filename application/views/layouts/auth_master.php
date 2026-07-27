<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Auth Master — login / forgot password pages (no app chrome)
 */
$this->load->view('layouts/header');
?>
<?php
if (!empty($content_view)) {
  $this->load->view($content_view, isset($content_data) ? $content_data : array());
}
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url('assets/js/theme.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/components.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/auth.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
