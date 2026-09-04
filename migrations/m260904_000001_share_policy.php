<?php

use humhub\components\Migration;

class m260904_000001_share_policy extends Migration
{
    public function safeUp()
    {
        $this->createTable('sharebetween_policy', [
            'content_id' => $this->integer()->notNull(),
            'allowed' => $this->boolean()->notNull()->defaultValue(false),
            'PRIMARY KEY ([[content_id]])',
        ]);
        $this->addForeignKey('fk-sharebetween_policy-content_id', 'sharebetween_policy', 'content_id', 'content', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('sharebetween_policy');
    }
}
