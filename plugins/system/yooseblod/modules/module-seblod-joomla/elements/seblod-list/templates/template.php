<?php

$el = $this->el('div', [
    'class' => [
        'Grrrr...'
    ],
]);

// Title
$list = $this->el('div', [
    'class' => [
        'el-title-MY'
    ],
]);

?>

<?= $el($props, $attrs) ?>

    <?php if ($props['list']): ?>
    <?= $list($props, $props['list']) ?>
    <?php endif; ?>

<?= $el->end() ?>
