<?php
/* @var $this ProfileController */
/* @var $data Profile */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('userid')); ?>:</b>
	<?php echo CHtml::encode($data->userid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('birthdate')); ?>:</b>
	<?php echo CHtml::encode($data->birthdate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('website')); ?>:</b>
	<?php echo CHtml::encode($data->website); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bio')); ?>:</b>
	<?php echo CHtml::encode($data->bio); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('picture')); ?>:</b>
	<?php echo CHtml::encode($data->picture); ?>
	<br />


</div>