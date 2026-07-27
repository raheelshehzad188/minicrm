<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Vendor JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<!-- Mino JS -->
<script src="<?php echo base_url('assets/js/theme.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/layout.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/components.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/dashboard.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/auth.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/settings.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/users.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/leads.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>

<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
