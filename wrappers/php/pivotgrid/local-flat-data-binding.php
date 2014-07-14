<?php
require_once '../lib/DataSourceResult.php';
require_once '../lib/Kendo/Autoload.php';

require_once '../include/header.php';

$db = new PDO('sqlite:..//sample.db');
$statement = $db->prepare("SELECT p.ProductName, p.UnitPrice, p.Discontinued, c.CategoryName FROM Products as p JOIN Categories c ON c.CategoryID = p.CategoryID");
$statement->execute();
$data = $statement->fetchAll(PDO::FETCH_ASSOC);

$model = new \Kendo\Data\DataSourceSchemaModel();
$productNameField = new \Kendo\Data\DataSourceSchemaModelField('ProductName');
$productNameField->type('string');

$unitPriceField = new \Kendo\Data\DataSourceSchemaModelField('UnitPrice');
$unitPriceField->type('number');

$discontinuedField = new \Kendo\Data\DataSourceSchemaModelField('Discontinued');
$discontinuedField->type('boolean');

$model->addField($productNameField)
      ->addField($unitPriceField)
      ->addField($discontinuedField);

$productNameDimension = new \Kendo\Data\PivotDataSourceSchemaCubeDimension('ProductName');
$productNameDimension->caption('All Products');

$categoryNameDimension = new \Kendo\Data\PivotDataSourceSchemaCubeDimension('CategoryName');
$categoryNameDimension->caption('All Categories');

$discontinuedDimension = new \Kendo\Data\PivotDataSourceSchemaCubeDimension('Discontinued');
$discontinuedDimension->caption('Discontinued');

$sumMeasure = new \Kendo\Data\PivotDataSourceSchemaCubeMeasure('Sum');
$sumMeasure->format('{0:c}')
            ->field('UnitPrice')
            ->aggregate('function(value, state) { return value + state; }');

$cube = new \Kendo\Data\PivotDataSourceSchemaCube();
$cube->addDimension($productNameDimension)
    ->addDimension($categoryNameDimension)
    ->addDimension($discontinuedDimension)
    ->addMeasure($sumMeasure);

$schema = new \Kendo\Data\PivotDataSourceSchema();
$schema->model($model)
       ->cube($cube);

$categoryColumn = new \Kendo\Data\PivotDataSourceColumn();
$categoryColumn->name('CategoryName')
            ->expand(true);

$productNameColumn = new \Kendo\Data\PivotDataSourceColumn();
$productNameColumn->name('ProductName');

$discontinuedRow = new \Kendo\Data\PivotDataSourceRow();
$discontinuedRow->name('Discontinued')
                ->expand(true);

$dataSource = new \Kendo\Data\PivotDataSource();

$dataSource ->addColumn($categoryColumn, $productNameColumn)
            ->addRow($discontinuedRow)
            ->addMeasure(array('Sum'))
            ->data($data)
            ->schema($schema);

$pivotgrid = new \Kendo\UI\PivotGrid('pivotgrid');
$pivotgrid->dataSource($dataSource)
    ->columnWidth(120)
    ->height(570);
?>

<?php
echo $pivotgrid->render();
?>

<?php require_once '../include/footer.php'; ?>
