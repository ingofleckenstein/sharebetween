<?php

use humhub\modules\content\widgets\stream\StreamEntryWidget;
use humhub\modules\content\widgets\stream\WallStreamEntryOptions;
use humhub\modules\sharebetween\models\Share;
use humhub\modules\sharebetween\models\SharePolicy;
use humhub\modules\user\models\User;

/* @var $share Share */
?>
<?php $original = $share->getContentRecord(); ?>
<?php if ($original->content->canView()) : ?>
    <?= StreamEntryWidget::renderStreamEntry($original) ?>
<?php elseif (!$original->content->isPublic()
    && $original->content->container instanceof User
    && SharePolicy::isAllowed($original->content)) : ?>
    <?= StreamEntryWidget::renderStreamEntry(
        $original,
        (new WallStreamEntryOptions())->disableControlsMenu()->disableAddons(),
    ) ?>
<?php else : ?>
    <div class="wall-entry">
        <p><?= Yii::t('SharebetweenModule.base', 'Content not available') ?></p>
        <?= Yii::t('SharebetweenModule.base', 'This content has either been deleted or you no longer have permission to access it.') ?>
    </div>
<?php endif; ?>
