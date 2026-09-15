<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>
    <?= Form::open([
        'id' => $this->formGetId(),
        'class' => 'layout',
        'data-change-monitor' => 'true',
        'data-window-close-confirm' => 'true',
    ]) ?>
        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons p-t">
            <?= $this->formMakePartial('toolbar') ?>
        </div>
    <?= Form::close() ?>
<?php else: ?>
    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
<<<<<<< HEAD
    <p><a href="<?= isset($formConfig) ? Backend::url($formConfig->defaultRedirect) : 'javascript:history.back()' ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
=======
    <p><a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
<?php endif ?>
