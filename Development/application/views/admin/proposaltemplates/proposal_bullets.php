<?php
$disabled = "";
   $bullets = array(
         array('name' => 'TITLE & INTRO','color'=>'','icon'=>'fa-file-text-o', 'id'=>'introduction_step'),
         array('name' => 'QUOTE','color'=>'#00bcd4','icon'=>'fa-list-ul', 'id'=>'quote_step' ),
         array('name' => 'PAYMENTS','color'=>'#056378','icon'=>'fa-calendar-o', 'id'=>'payments_step' ),
         array('name' => 'AGREEMENT','color'=>'#9c27b0','icon'=>'fa-file-text-o' , 'id'=>'agreement_step'),
         array('name' => 'MESSAGE','color'=>'' ,'icon'=>'fa-user', 'id'=>'message_step'),
         array('name' => 'GALLERY','color'=>'#f44336','icon'=>'fa-picture-o', 'id'=>'gallery_step' ),
         array('name' => 'FILES','color'=>'#ff9800','icon'=>'fa-folder-open-o', 'id'=>'files_step' ),
         array('name' => 'SIGNATURES','color'=>'#4caf50' ,'icon'=>'fa-pencil', 'id'=>'signatures_step'),
      );
   $numStatuses = count($bullets);
   $processwidth=100 / $numStatuses; ?>
<div class="_buttons">
   <div class="row leads-overview proposal_overview" style="display: block;">
   <?php foreach($bullets as $status){
       $id = $status['name'];
       if($status['name']=="TITLE & INTRO"){
           $id = "introduction";
       }
       if(isset($sections)) {
         if(in_array(strtolower($id),$sections)){
             $disabled = "disbaled";
         }else{
             $disabled = "";
         }
        } else {
          $disabled = "";
        }
       ?>
      <div id= "<?php echo $status['id'];?>" class="process-step <?php echo $disabled ?>" style="width:<?php echo $processwidth . '%' ; ?>">
         <a href="#<?php echo strtolower($id); ?>">
         <h3 class="bold" style="background-color:<?php echo $status['color']; ?>"><i class="fa <?php echo $status['icon']; ?>"></i></h3>
         <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span></a>
      </div>
   <?php } ?>
   </div> 
</div>