<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use yii\db\Migration;

/**
 * Handles the creation of table `{{%common_attachment}}`.
 */
class m220825_043920_create_attachment_table extends Migration
{
    private string $tableName = '{{%common_attachment}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'member_id' => $this->integer()->null()->defaultValue(0)->comment('Uploader'),
            'drive' => $this->string(50)->null()->defaultValue('local')->comment('Driver'),
            'file_type' => $this->string(10)->null()->defaultValue('image')
                ->comment('Upload type'),
            'specific_type' => $this->string(100)->null()->defaultValue('')->comment('Specific type'),
            'base_url' => $this->string(1024)->null()->defaultValue('')->comment('Base URL'),
            'path' => $this->string(1024)->null()->defaultValue('')->comment('File path'),
            'poster' => $this->string(1024)->null()->defaultValue('')->comment('Video poster'),
            'hash' => $this->string(100)->null()->defaultValue('')->comment('File hash'),
            'name' => $this->string(200)->null()->defaultValue('')->comment('Original name'),
            'extension' => $this->string(50)->null()->defaultValue(0)->comment('Extension'),
            'size' => $this->integer()->null()->defaultValue(0)->comment('File size'),
            'year' => $this->integer()->null()->defaultValue(0)->comment('Year'),
            'month' => $this->integer()->null()->defaultValue(0)->comment('Month'),
            'day' => $this->integer()->null()->defaultValue(0)->comment('Day'),
            'width' => $this->integer()->null()->defaultValue(0)->comment('Width'),
            'height' => $this->integer()->null()->defaultValue(0)->comment('Height'),
            'duration' => $this->string(50)->null()->defaultValue('')->comment('Duration'),
            'upload_ip' => $this->string(50)->null()->defaultValue('')->comment('Upload IP'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Common attachment table');
        $this->createIndex('IDX_File_Hash',$this->tableName,'hash',1);
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('IDX_File_Hash', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
