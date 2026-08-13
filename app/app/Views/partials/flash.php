<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning"><?= esc(session()->getFlashdata('warning')) ?></div>
<?php endif; ?>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-error">
        <ul style="margin:0;padding-left:18px">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc(is_array($err) ? implode(', ', $err) : $err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
