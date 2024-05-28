<!DOCTYPE html>
<html lang="<?= $langcode ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/">
    <title><?= $pagetitle ?></title>
    <?php if ($nocache) : ?>
        <meta http-equiv="Cache-control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
    <?php endif; ?>
    <?= $this->renderHeadAssets(); ?>
</head>

<body>
    <?= $this->renderPartial('menu'); ?>
    <?= $this->renderPartial('footer'); ?>
    <?= $this->renderBodyAssets(); ?>
</body>

</html>