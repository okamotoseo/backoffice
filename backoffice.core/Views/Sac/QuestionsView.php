		<div class="row">
		     <div class="col-sm-3">
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Arquivo</h3>
                  <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body no-padding">
                  <ul class="nav nav-pills nav-stacked">
                    <li class="<?=$unanswered;?>"><a href="/Sac/Questions/"><i class="fa fa-comments-o"></i> Aguardando Resposta <span class="label label-primary pull-right"><?php  echo totalQuestionStatus($this->db, $this->storedata['id'], 'UNANSWERED'); ?></span></a></li>
                    <li class="<?=$answered;?>"><a href="/Sac/Questions/Answered"><i class="fa fa-comments"></i> Respondidas <span class="label label-primary pull-right"><?php  echo totalQuestionStatus($this->db, $this->storedata['id'], 'ANSWERED'); ?></span></a></li>
                  </ul>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->
            <div class="col-sm-9">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Perguntas e Respostas</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-default btn-sm update_questions"><i class="fa fa-repeat"></i></button>
                  </div><!-- /.box-tools -->
                </div><!-- /.box-header -->
                
                <div class="box-body no-padding">
                    <table class="table table-hover table-striped table-condensed">
                      <tbody>
                      
                      <?php 
                      
                      $answered = '';
                      $unanswered = 'active';
                      if($parametros[2] == 'Answered'){
                      	$answered = 'active';
                      	$unanswered = '';
                      }
                      
                      foreach($questions as $key => $question){
                      	$item = $question['item'];
                      	$price = $item['price'];
                      	$questionVal = trim($question['question']);
                      	$answerVal = trim($question['answer']);
                      	$customer = isset($question['customer']) ? $question['customer'] : '';
                      	
                      	if($question['marketplace'] == 'Mercadolivre'){
                      		$link = $item['permalink'];
                      	}
//                       	if(strlen($questionVal) > 55){
//                       		$questionVal = substr($questionVal, 0, 55);
//                       		$questionVal .="...";
//                       	}
                        echo "<tr id='tr-{$question['id']}'>
                          <td id='body-{$question['id']}'>
                          <ul class='products-list product-list-in-box'>
		                    <li class='item' style='background: rgba(255,255,255,0.15);'>
		                      <div class='product-img'>
		                        <img src='{$item['thumbnail']}'  alt='Imagem produto' width='46px' height='46px' alt='Product Image'>
		                      </div>
		                      <div class='product-info'>
		                        <a href='{$link}'  class='product-title'  target='_blank'>{$item['title']}</a>
		                        <a href='/Products/Product/{$item['ap_id']}' target='_blank'><i class='fa  fa-pencil-square-o pull-right'></i></a>
		                        <span class='product-description'>
		                        <b>SKU:</b> {$item['sku']} - <b>Qtd.:</b> {$item['ap_quantity']} - <b>Preço:</b> {$price}
		                        </span>
		                      </div>
		                    </li>
		                    </ul>
                          	<div class='direct-chat-msg left'>
			                      	<div class='direct-chat-info clearfix'>
			                        	<span class='direct-chat-name pull-left'>{$customer}</span>
			                        	<span class='direct-chat-timestamp pull-right'>".dateTimeBr($question['date_created'], '/')."</span>
			                      	</div>
		                      		<img class='direct-chat-img' src='/Views/_uploads/images/profile/160x160/default.png' alt='message user image'>
		                        	<div class='direct-chat-text'>{$questionVal}</div>
	                          	</div>";
	                          
	                          if(!empty($answerVal)){
			                  	echo "<div class='direct-chat-msg right '>
			                      	<div class='direct-chat-info clearfix'>
			                        <span class='direct-chat-name pull-right'>{$question['user']}</span>
			                        <span class='direct-chat-timestamp pull-left'>".dateTimeBr($question['answer_date_created'], '/')."</span>
			                      </div>
			                      <img class='direct-chat-img' src='/Views/_uploads/images/store/160x160/{$questionsModel->store_id}.png' alt='message user image'>
			                      <div class='direct-chat-text'>{$answerVal}</div>
			                    </div>";
	                          }
		                     if($question['status'] != 'ANSWERED'){
		                      echo "<div class='input-group input-group-sm' id='input-group-{$question['id']}'>
			                      <input type='text' name='message' placeholder='Resposta ...' class='form-control' id='answer-{$question['id']}'>
			                      <span class='input-group-btn'>
			                      	<button type='button' class='btn btn-primary btn-flat send-answer' id='{$question['id']}' marketplace='{$question['marketplace']}' ><i class='fa  fa-send'></i></button>
			                      </span>
		                      </div>";
                    		}
		                      
		                      
	                      echo "</td>
                        </tr>";
                        }
                        ?>
                      </tbody>
                    </table><!-- /.table -->
                </div><!-- /.box-body -->
               <div class="overlay" style='display:none;'>
               		<i class="fa fa-refresh fa-spin"></i>
               </div>
              </div><!-- /. box -->
            </div><!-- /.col -->
            
            

          </div><!-- /.row -->

<?php 