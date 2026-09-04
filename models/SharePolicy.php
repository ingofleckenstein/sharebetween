<?php

namespace humhub\modules\sharebetween\models;

use humhub\components\ActiveRecord;
use humhub\modules\content\models\Content;

class SharePolicy extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'sharebetween_policy';
    }

    public function rules(): array
    {
        return [
            [['content_id'], 'required'],
            [['content_id'], 'integer'],
            [['allowed'], 'boolean'],
        ];
    }

    public static function isAllowed(Content $content): bool
    {
        $policy = static::findOne(['content_id' => $content->id]);
        return $policy === null ? $content->isPublic() : (bool) $policy->allowed;
    }

    public static function setAllowed(Content $content, bool $allowed): bool
    {
        $policy = static::findOne(['content_id' => $content->id]) ?? new static(['content_id' => $content->id]);
        $policy->allowed = $allowed;
        return $policy->save();
    }
}
