<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8842 2015-05-04 20:34:47Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/**расскомметнить (удалить эту строку) или вкл ласт модиф
$time = $this->product->modified_on;
$date = new DateTime($time);
$LastModified_unix = $date->getTimestamp(); // время последнего изменения страницы
$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
$IfModifiedSince = false;
if (isset($_ENV['HTTP_IF_MODIFIED_SINCE']))
$IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
$IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
if ($IfModifiedSince && $IfModifiedSince >= $LastModified_unix) {
header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
exit;
}
header('Last-Modified: '. $LastModified);

   // $last_modified=strtotime($this->detail->modified);

   // JResponse::setHeader('Last-Modified',gmdate('D, d M Y H:i:s', $last_modified).' GMT');

   // if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])>=$last_modified){

    	//header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');

    	//die;

    //	}

/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));



if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>
<!--Указывается схема Product.-->

<div class="productdetails-view productdetails">

    <div class="vm-product-wrap row">
	
	
		<div class="vm-product-media-img col-sm-5">
		<div class="customfields">
		<?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'custom')); ?>
		<?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$product,'position'=>'custom')); ?></div>
			<?php
			echo $this->loadTemplate('images');

			$count_images = count ($this->product->images);
			if ($count_images > 1) {
				echo $this->loadTemplate('images_additional');
			}
			?>
		</div> <!--/.col-sm-5-->

		<div class="vm-product-details-inner col-sm-7">
		<?php
                      $desc = explode('<hr id="system-readmore" />', $this->product->product_desc);
                       if($desc[1]){ ?>
                        <div class="product-description description_top"><?php echo $desc[0]; ?></div>                    
                         <?php } else { ?>
                          <?php } ?> 
		<?php echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal')); ?>
			<div class="not_del"><?php //echo vmText::_('COM_VIRTUEMART_PRODUCT__NOT_DELIVER');?>
				 <?php jimport( 'joomla.application.module.helper' ); 
                       $modules = JModuleHelper::getModules( '1-product-position');  
                       $attribs['style'] = 'xhtml'; 
                       foreach($modules as $module){echo JModuleHelper::renderModule($module, $attribs);} 
                 ?>
				 </div>
			<div class="vm-product-title">
				<div class="pull-left">
					<?php // Product Title   ?>
					 
						    <?php // Product Title END   ?>
					 <?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product)); ?>
				</div> 
				<div class="pull-right">
				    <div class="price-right">
				    	<?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency)); ?> 
				   	</div>
				</div>
				<div class="clearfix"></div>
			</div>

		    <?php echo $this->product->event->afterDisplayTitle ?>

			<?php
			if (is_array($this->productDisplayShipments)) {
				echo '<div class="vm-product-shipments">';
			    foreach ($this->productDisplayShipments as $productDisplayShipment) {
					echo '<div class="vm-product-shipment">';
					echo $productDisplayShipment . '<br />';
					echo '</div>';
			    }
				echo '</div>';
			}
			if (is_array($this->productDisplayPayments)) {
			    foreach ($this->productDisplayPayments as $productDisplayPayment) {
					echo $productDisplayPayment . '<br />';
			    }
			}

			//In case you are not happy using everywhere the same price display fromat, just create your own layout
			//in override /html/fields and use as first parameter the name of your file
			//echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency)); ?> 

			<?php

			echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
		    ?>
			<div class="avstat">
                        <?php if($product->product_availability){
                            $availPathImage = VmConfig::get('assets_general_path') . 'images/availability/' . $product->product_availability;
                            echo file_exists(JPATH_BASE.DS.$availPathImage)? JHTML::image(JURI::root().$availPathImage, $product->product_availability, array('class' => 'availability')) : JText::_($product->product_availability); 
                        } 
                        ?>
                    </div>  

					<?php
			// Availability
			$stockhandle = VmConfig::get('stockhandle', 'none');
			$product_available_date = substr($this->product->product_available_date,0,10);
			$current_date = date("Y-m-d");
			if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
				if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
				?>	<div class="availability">
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHTML::_('date', $this->product->product_available_date, JText::_('DATE_FORMAT_LC')); ?>
					</div>
				<?php
				} else if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($this->product->product_availability)) {
				?>	<div class="availability">
					<?php echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABILITY') .': ',  (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : JText::_(VmConfig::get('rised_availability')); ?>
				</div>
				<?php
				} else if (!empty($this->product->product_availability)) {
				?>
				
				<?php
				}
			}
			else if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
			?>	<div class="availability">
					<?php echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHTML::_('date', $this->product->product_available_date, JText::_('DATE_FORMAT_LC')); ?>
				</div>
			<?php
			}
			?>  
            <div class="availability">
                <?php //echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABILITY') .': ', (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability)) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability, $this->product->product_availability, array('class' => 'availability')) : JText::_($this->product->product_availability); ?>
                </div>
					
    		<?php if (!empty($this->product->product_s_desc)) { ?>
		     <h5> <?php echo JText::_('PRODUCT_SHORT_DESC');?> </h5>
			  <div class="product-short-description ">
			    <?php
				    /** @todo Test if content plugins modify the product description */
				    echo nl2br($this->product->product_s_desc);
				    ?>
		        </div>
			   <?php } // Product Short Description END ?>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'location')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'available')); ?></div>  
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'roots')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'period')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'height')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'old')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'flavour')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'circl')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'pickup')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'delivery')); ?></div>
			 <div class="customfields"><?php //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'custom')); ?></div>
			
				<?php
				// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
				/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
				  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
				  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
				 */
				?>

				<div class="clear"></div>
				<?php
				//echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));
                echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product,'position'=>'addcf'));
				//echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

				// Ask a question about this product
				if (VmConfig::get('ask_question', 0) == 1) {
					$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
					?>
					<div class="ask-a-question">
						<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
					</div>
				<?php
				}
				?>

				<?php
				// Manufacturer of the Product
				if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
				    echo $this->loadTemplate('manufacturer');
				}
				?>
		    </div>

		    <?php
		    // Product Navigation
		    if (VmConfig::get('product_navigation', 1)) {
			?>
		        <div class="product-neighbours">
				    <?php
				    if (!empty($this->product->neighbours ['previous'][0])) {
					$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
					echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
						['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
				    }
				    if (!empty($this->product->neighbours ['next'][0])) {
					$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
					echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
				    }
				    ?>
			    	<div class="clear"></div>
		        </div>
			    <?php } // Product Navigation END
			    ?>

				

			    <?php
			    // Product Edit Link
			    echo $this->edit_link;
			    // Product Edit Link END
			    ?>

			    <?php
			    // PDF - Print - Email Icon
			    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
				?>
			        <div class="icons">
				    <?php

				    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

					echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
				    //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
					echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
					$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
				    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
				    ?>
			    	<div class="clear"></div>
			        </div>
			    <?php } // PDF - Print - Email Icon END
			    ?>

		</div> <!--/.col-sm-7-->
		<div class="clear"></div>
    </div> <!--/.row-->

    <div class="row">
	    <div class="col-sm-12">
			<?php
			// event onContentBeforeDisplay
			echo $this->product->event->beforeDisplayContent; ?>

			<div class="products-desc-tab">

				<ul id="myTab" class="nav nav-tabs">
				
					<?php if (!empty($this->product->product_desc)) { ?>
					<li class="active">
					<a href="#deliver" data-toggle="tab">
						   <?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DELIVER_PAY');?>
					  </a>
					</li>
				    <?php } // Product Description END ?>

				 	
					
					<li>
					  	<a href="#desc" data-toggle="tab">
							<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?>
						 </a>
					</li>	
				</ul>
			 
				<div id="myTabContent" class="tab-content">
                 
				  <div class="tab-pane fade active in" id="deliver">
			
			 		 
<?php
$document    = &JFactory::getDocument();
$renderer    = $document->loadRenderer('modules');
$options    = array('style' => 'header'); //тут стиль вывода модуля, можно оставить как тут
$position    = 'deliver'; //тут название позиции (ее можно задать при публикации модуля, можно просто вписать туда rand как тут)
echo $renderer->render($position, $options, null);
?>  				
</div>

				    
					 
					 <div class="tab-pane fade" id="desc">
					 <div class="title-product">
					<?php // Product Title   ?>
			 <h2><?php echo $this->product->product_name ?></h2>
<?php // Product Title END   ?>
				</div> 	
				    	<?php if($desc[1]){ ?>
        <div class="product-description description_bottom"><?php echo $desc[1]; ?></div>
<?php } else { ?> <div class="product-description"> <?php echo $this->product->product_desc; ?> </div>
<?php } ?>
				
					<?php if (!empty($this->product->product_desc)) { ?>
					    
						    
					    </div>
				    <?php } // Product Description END ?>
						
				 	 </div>			 
				</div>

			</div> 
			<!--/. products-desc-tab-->
			<div class="relatedbox"><?php 
			echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

			?></div>
<?php
		    // Product Packaging
		    $product_packaging = '';
		    if ($this->product->product_box) {
			?>
		        <div class="product-box">
			    <?php
			        echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
			    ?>
		        </div>
		    <?php } // Product Packaging END ?>

		    <?php 
			echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

		   // echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

			echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));

			?>

			<?php // onContentAfterDisplay event
			echo $this->product->event->afterDisplayContent;

			// Show child categories
			if (VmConfig::get('showCategory', 1)) {
				//echo $this->loadTemplate('showcategory');
			} ?>

		</div> <!--/.col-sm-12-->
	</div> <!--/.row-->
<div class=sharetitle> <?php echo JText::_('SHARE_SOC');?> </div>
<div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,viber,whatsapp,skype" data-size="s"></div>
		    <div class="spacer-buy-area">
<?php // Back To Category Button
				if ($this->product->virtuemart_category_id) {
					$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
					$categoryName = vmText::_($this->product->category_name) ;
				} else {
					$catURL =  JRoute::_('index.php?option=com_virtuemart');
					$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
				}
				?>
				<div class="back-to-category">
			    	<a href="<?php echo $catURL ?>" class="abacktocat" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
				</div>
	<?php

	$j = 'jQuery(document).ready(function($) {
		Virtuemart.product(jQuery("form.product"));

		$("form.js-recalculate").each(function(){
			if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
				var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
				Virtuemart.setproducttype($(this),id);

			}
		});
	});';
	//vmJsApi::addJScript('recalcReady',$j);

	/** GALT
		 * Notice for Template Developers!
		 * Templates must set a Virtuemart.container variable as it takes part in
		 * dynamic content update.
		 * This variable points to a topmost element that holds other content.
		 */
	$j = "Virtuemart.container = jQuery('.productdetails-view');
	Virtuemart.containerSelector = '.productdetails-view';";

	vmJsApi::addJScript('ajaxContent',$j);

	echo vmJsApi::writeJS();
	
	if ($this->product->prices['salesPrice'] > 0) {
	echo shopFunctionsF::renderVmSubLayout('snippets',array('product'=>$this->product, 'currency'=>$this->currency, 'showRating'=>$this->showRating));
}
	?> 
</div> <!--/.productdetails-->



