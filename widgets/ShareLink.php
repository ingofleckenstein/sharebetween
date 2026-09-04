<?php

namespace humhub\modules\sharebetween\widgets;

use humhub\helpers\Html;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\sharebetween\models\Share;
use humhub\modules\sharebetween\models\SharePolicy;
use humhub\modules\sharebetween\services\ShareService;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class ShareLink extends Widget
{
    /**
     * @var ContentActiveRecord
     */
    public $record;

    public function run()
    {
        if ($this->record instanceof Share) {
            return '';
        }

        $content = $this->record->content;
        $isPrivateProfileContent = !$content->isPublic() && $content->container instanceof User;

        if (!$content->isPublic() && !$isPrivateProfileContent) {
            return '';
        }

        if (!$this->record->content->getStateService()->isPublished()) {
            return '';
        }

        if (Yii::$app->user->isGuest) {
            return '';
        }

        $isOwner = (int) $content->created_by === (int) Yii::$app->user->id;
        $isShareAllowed = SharePolicy::isAllowed($content);
        if (!$isOwner && !$isShareAllowed) {
            return '';
        }

        $linkOptions = [
            'data-action-click' => 'ui.modal.load',
            'data-action-click-url' => Url::toRoute(['/sharebetween/share', 'id' => $content->id]),
        ];

        if ($isOwner) {
            $label = $isShareAllowed
                ? Html::tag('i', '', ['class' => 'fa fa-share-alt', 'aria-hidden' => 'true']) . ' '
                    . Yii::t('SharebetweenModule.base', 'Shareable')
                : Html::tag('i', '', ['class' => 'fa fa-lock', 'aria-hidden' => 'true']) . ' '
                    . Yii::t('SharebetweenModule.base', 'Sharing blocked');
            $tooltip = $isShareAllowed
                ? Yii::t('SharebetweenModule.base', 'Other users can share this content. Click here to change this setting.')
                : Yii::t('SharebetweenModule.base', 'This content cannot currently be shared. Click here to allow sharing.');
            Html::addTooltip($linkOptions, $tooltip);
            $linkOptions['aria-label'] = $tooltip;
        } else {
            $label = Yii::t('SharebetweenModule.base', 'Share');
        }

        return Html::tag(
            'span',
            Html::a(
                $label . $this->getCounter(),
                '#',
                $linkOptions,
            ),
            ['class' => 'share-between-container'],
        );
    }

    private function getCounter()
    {
        $count = count((new ShareService($this->record, Yii::$app->user->getIdentity()))->list());
        if ($count === 0) {
            return '';
        }

        return sprintf(' (%d)', $count);
    }
}
