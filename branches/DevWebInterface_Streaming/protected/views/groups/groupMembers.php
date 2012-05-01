<?php

if ($dataProvider != null) 
{
	//TODO: Refactor make common confirmation dialog 	
	/** This is the group member id holder, when user clicks delete, its content is filled***/
	echo "<div id='groupMemberId' style='display:none'></div>";
	echo "<div id='groupId' style='display:none'></div>";
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupMemberDeleteConfirmation',
		// additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Delete Group Member'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'buttons' =>array (
				"OK"=>"js:function(){
							". CHtml::ajax(
									array(
											'url'=>Yii::app()->createUrl('groups/deleteGroupMember'),
											'data'=> array('userId'=>"js:$('#groupMemberId').html()", 'groupId'=>"js:$('#groupId').html()"),
											'success'=> 'function(result) { 	
														 	try {
														 		$("#groupMemberDeleteConfirmation").dialog("close");
																var obj = jQuery.parseJSON(result);
																if (obj.result && obj.result == "1") 
																{
																	$.fn.yiiGridView.update($("groupMembersListView").text());
																}
																else 
																{
																	$("#messageDialogText").html("Sorry,an error occured in operation - 1");
																	$("#messageDialog").dialog("open");
																}

															}
															catch(ex) {
																$("#messageDialogText").html("Sorry,an error occured in operation - 2");
																$("#messageDialog").dialog("open");
															}
														}',
										)) .
						"}",
			"Cancel"=>"js:function() {
				$( this ).dialog( \"close\" );
			}" 
			)),
		));	
	echo "Do you want to delete this group member?";
	$this->endWidget('zii.widgets.jui.CJuiDialog');	
	

	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'groupMembersListView',
			'summaryText'=>'',
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		array(            // display 'create_time' using an expression
				    'name'=>'Name',
					'type' => 'raw',		
					'sortable'=>true,	
					'value'=>'$data["Name"]',	
		),
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
					'type' => 'raw',
            'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
								array("onclick"=>"$(\"#groupMemberId\").text(".$data[\'userId\'].");
												  $(\"#groupId\").text(".$data[\'groupId\'].");
												  $(\"#groupMemberDeleteConfirmation\").dialog(\"open\");", 
										"class"=>"vtip", 
										"title"=>"Delete Group Member"))',

					'htmlOptions'=>array('width'=>'16px')
		),
	),
	));
						
}
/*
 */
?>