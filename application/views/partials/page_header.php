<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mino-page-header">
  <div>
    <?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
      <nav aria-label="breadcrumb">
        <ol class="mino-breadcrumb">
          <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <?php if (!empty($crumb['url']) && $i < count($breadcrumbs) - 1): ?>
              <li class="mino-breadcrumb-item"><a href="<?php echo $crumb['url']; ?>"><?php echo html_escape($crumb['label']); ?></a></li>
            <?php else: ?>
              <li class="mino-breadcrumb-item active"><?php echo html_escape($crumb['label']); ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </nav>
    <?php endif; ?>

    <?php if (!empty($page_title)): ?>
      <h1 class="mino-page-title"><?php echo html_escape($page_title); ?></h1>
    <?php endif; ?>
    <?php if (!empty($page_subtitle)): ?>
      <p class="mino-page-subtitle"><?php echo html_escape($page_subtitle); ?></p>
    <?php endif; ?>
  </div>

  <?php if (!empty($page_actions)): ?>
    <div class="mino-page-actions">
      <?php echo $page_actions; ?>
    </div>
  <?php endif; ?>
</div>
