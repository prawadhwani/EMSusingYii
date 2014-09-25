<?php
/* @var $this ProfileController */
/* @var $model Profile */

$this->breadcrumbs=array(
	'Profiles'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Profile', 'url'=>array('index')),
	array('label'=>'Create Profile', 'url'=>array('create')),
	array('label'=>'Update Profile', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Profile', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Profile', 'url'=>array('admin')),
    //array('label'=>'Add Manager To Profile', 'url'=>array('adduser', 'id'=>$model->id)),
    array('label' => 'Request Leave', 'url'=>array('leave/create', 'pid'=>$model->id)),
);

if(Yii::app()->user->checkAccess('manager',array('profile'=>$model)))
{
    $this->menu[] = array('label'=>'Add Manager To Profile','url'=>array('adduser', 'id'=>$model->id));
}

?>

<h1>View Profile #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'birthdate',
		'website',
		'bio',
        array(
            'label'=>'Picture',
            'type'=>'raw',
            'value'=> CHtml::image(Yii::app()->request->baseUrl.'/protected/image/'.$model->picture),
        ),
	),
)); ?>

<br><br>
<h3>Request Leave</h3>
<h6>Enter the dates with a valid reason!</h6>
<?php if(Yii::app()->user->hasFlash('leaveSubmitted')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('leaveSubmitted'); ?>
    </div>
<?php else: ?>
    <?php $this->renderPartial('/leave/_form',array(
        'model'=>$leave,
    )); ?>
<?php endif; ?>

<h3>Approved Leaves</h3>
    <?php $this->renderPartial('_leaves',array(
        'profile'=>$model,
        'leaves'=>$model->leaves,
    )); ?>
