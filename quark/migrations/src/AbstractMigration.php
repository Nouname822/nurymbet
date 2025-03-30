<?php

namespace Quark\Migrations;

abstract class AbstractMigration
{
    abstract public function up(): string;
    abstract public function down(): string;
}
