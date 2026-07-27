<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Master Layout — all authenticated app pages
 */
$this->load->view('layouts/header');
?>
<div class="mino-app<?php echo !empty($sidebar_collapsed) ? ' sidebar-collapsed' : ''; ?>">
  <?php $this->load->view('layouts/sidebar'); ?>

  <div class="mino-main">
    <?php $this->load->view('layouts/navbar'); ?>

    <main class="mino-content">
      <?php $this->load->view('partials/page_header'); ?>

      <?php
      if (!empty($content_view)) {
        $this->load->view($content_view, isset($content_data) ? $content_data : array());
      }
      ?>
    </main>

    <?php $this->load->view('layouts/footer'); ?>
  </div>
</div>

<?php $this->load->view('partials/right_panel'); ?>
<?php $this->load->view('layouts/scripts'); ?>
