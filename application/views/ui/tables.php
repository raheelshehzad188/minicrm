<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mino-alert mino-alert-primary">
  <i class="fas fa-table"></i>
  <div>Professional DataTable pattern with search, filter, bulk select, status badges, and responsive layout.</div>
</div>

<div class="mino-table-wrap">
  <div class="mino-table-toolbar">
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <button type="button" class="btn btn-sm btn-secondary"><i class="fas fa-filter"></i> Filters</button>
      <button type="button" class="btn btn-sm btn-ghost" data-mino-toast data-title="Exported (UI demo)" data-icon="info"><i class="fas fa-download"></i> Export</button>
      <div class="dropdown">
        <button class="btn btn-sm btn-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown">Bulk actions</button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#"><i class="fas fa-user-tag"></i> Assign owner</a></li>
          <li><a class="dropdown-item" href="#"><i class="fas fa-tag"></i> Change status</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#" data-mino-confirm data-title="Delete selected?"><i class="fas fa-trash"></i> Delete</a></li>
        </ul>
      </div>
    </div>
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal"><i class="fas fa-plus"></i> Add Lead</button>
  </div>

  <div class="table-responsive p-3 pt-0">
    <table class="mino-datatable mino-table w-100" data-page-length="8">
      <thead>
        <tr>
          <th style="width:36px"><input type="checkbox" class="form-check-input" data-mino-check-all=".row-check"></th>
          <th>Name</th>
          <th>Company</th>
          <th>Status</th>
          <th>Source</th>
          <th>Value</th>
          <th>Owner</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $rows = array(
          array('Emma Watson', 'EW', 'BrightSoft', 'Qualified', 'success', 'Website', '$12,400', 'Alex Davis'),
          array('James Lee', 'JL', 'Nexus Labs', 'New', 'primary', 'Referral', '$8,200', 'Sarah Kim'),
          array('Olivia Park', 'OP', 'Peak Retail', 'Contacted', 'warning', 'LinkedIn', '$5,600', 'Mike Ross'),
          array('Daniel Ruiz', 'DR', 'Verde Corp', 'Cold', 'danger', 'Cold Call', '$3,100', 'Alex Davis'),
          array('Ava Chen', 'AC', 'Horizon Ltd', 'Proposal', 'info', 'Webinar', '$22,000', 'Sarah Kim'),
          array('Noah Patel', 'NP', 'CloudSync', 'Qualified', 'success', 'Website', '$15,800', 'Mike Ross'),
          array('Mia Brooks', 'MB', 'Studio North', 'New', 'primary', 'Ads', '$4,200', 'Alex Davis'),
          array('Liam Scott', 'LS', 'Orbit Pay', 'Contacted', 'warning', 'Referral', '$9,900', 'Sarah Kim'),
          array('Sophia Grant', 'SG', 'Leaf Health', 'Proposal', 'info', 'Partner', '$18,500', 'Mike Ross'),
          array('Ethan Moore', 'EM', 'Stackline', 'Cold', 'danger', 'Cold Call', '$2,400', 'Alex Davis'),
        );
        foreach ($rows as $r):
        ?>
        <tr>
          <td><input type="checkbox" class="form-check-input row-check"></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span class="mino-avatar mino-avatar-sm"><?php echo $r[1]; ?></span>
              <span class="fw-semibold"><?php echo $r[0]; ?></span>
            </div>
          </td>
          <td><?php echo $r[2]; ?></td>
          <td><span class="mino-badge mino-badge-<?php echo $r[4]; ?> mino-badge-dot"><?php echo $r[3]; ?></span></td>
          <td><?php echo $r[5]; ?></td>
          <td class="fw-semibold"><?php echo $r[6]; ?></td>
          <td><?php echo $r[7]; ?></td>
          <td>
            <div class="table-actions">
              <button type="button" class="btn btn-sm btn-ghost btn-icon" data-bs-toggle="tooltip" title="View"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-pen"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon text-danger" data-mino-confirm data-title="Delete lead?" title="Delete"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Lead Modal (UI only) -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Full name <span class="required">*</span></label>
          <input type="text" class="form-control" placeholder="Full name">
        </div>
        <div class="form-group">
          <label class="form-label">Company</label>
          <input type="text" class="form-control" placeholder="Company">
        </div>
        <div class="form-group mb-0">
          <label class="form-label">Status</label>
          <select class="form-select">
            <option>New</option>
            <option>Contacted</option>
            <option>Qualified</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-mino-toast data-title="Lead added (UI demo)">Save</button>
      </div>
    </div>
  </div>
</div>
