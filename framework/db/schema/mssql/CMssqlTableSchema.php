<?php
/**
 * CMssqlTableSchema class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CMssqlTableSchema represents the metadata for a MSSQL table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @version $Id: CMssqlTableSchema.php 2799 2011-01-01 19:31:13Z qiang.xue $
 * @package system.db.schema.mssql
 * @since 1.0.4
 */
class CMssqlTableSchema extends CDbTableSchema
{
	/**
	 * @var string name of the catalog (database) that this table belongs to.
	 * Defaults to null, meaning no schema (or the current database).
	 */
	public $catalogName;
	/**
	 * @var string name of the schema that this table belongs to.
	 * Defaults to null, meaning no schema (or the current database owner).
	 */
	public $schemaName;
}
