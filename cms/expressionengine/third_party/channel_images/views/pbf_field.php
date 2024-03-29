<?php if ($dupe_field == TRUE): ?>
<span style="font-weight:bold; color:red;"> <?=lang('ci:dupe_field')?> </span>
<input name="field_id_<?=$field_id?>[skip]" type="hidden" value="y" />
<?php elseif ($missing_settings == TRUE): ?>
<span style="font-weight:bold; color:red;"> <?=lang('ci:missing_settings')?> </span>
<input name="field_id_<?=$field_id?>[skip]" type="hidden" value="y" />
<?php else: ?>

<div class="CIField" id="ChannelImages<?=$field_id?>" rel="<?=$field_id?>">
<?php if ($this->config->item('is_site_on') != 'y'):?><p style="color:red; font-weight:bold;"><?=lang('ci:site_is_offline')?></p><?php endif;?>

<div class="TopActions"></div>
<table cellspacing="0" cellpadding="0" border="0" class="CITable">
	<thead>
		<tr>
			<th colspan="99" class="top_actions">
				<div class="block UploadImages"><?=lang('ci:upload_images')?><em id="ChannelImagesSelect"></em></div>
				<?php if ($settings['show_stored_images'] == 'yes'):?><div class="block StoredImages"><?=lang('ci:stored_images')?></div><?php endif;?>
				<div class="block">&nbsp;</div>
				<div class="block_long">
					<div class="UploadProgress hidden">
						<div class="progress">
							<div class="inner">
								<span class="percent"></span>&nbsp;&nbsp;&nbsp;
								<span class="speed"></span>&nbsp;&nbsp;&nbsp;
								<span class="bytes"> <span class="uploadedBytes"></span> <span class="totalBytes"></span> </span>&nbsp;&nbsp;&nbsp;
							</div>
						</div>
					</div>
				</div>
				<div class="block"><a href="#" class="StopUpload"><?=lang('ci:stop_upload')?></a></div>
			</th>
		</tr>
		<tr class="SearchImages" style="display:none">
			<th colspan="99">
			<?php if ($settings['stored_images_search_type'] == 'entry'):?>
				<table>
					<tbody>
						<tr>
							<td class="entryfilter">
								<div class="filter">
									<div class="left">
										<input type="text" value="<?=lang('ci:filter_keywords')?>" maxlength="256" onblur="if (value == '') {value='<?=lang('ci:filter_keywords')?>'}" onfocus="if (value == '<?=lang('ci:filter_keywords')?>') {value =''}">
									</div>
									<div class="right">
										<label><?=lang('ci:last')?></label>
										<select><option>100</option><option>200</option><option>300</option><option>400</option><option>500</option></select>
										<label><?=lang('ci:entries')?></label>
									</div>
								</div>
								<div class="entries">
									<p class="Loading"><?=lang('ci:loading_entries')?></p>
								</div>
							</td>
							<td class="entryimages">
								<div class="filter">
									<div class="left"><h4><?=lang('ci:entry_images')?></h4></div>
									<div class="right"><p class="SearchingForImages"><?=lang('ci:searching_images')?></p></div>
								</div>
								<div class="images">
									<p class="NoEntrySelect"><?=lang('ci:no_entry_sel')?></p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<?php else:?>
				<table>
					<tbody>
						<tr>
							<td class="imagefilter">
								<div class="filter">
									<div class="left">
										<?php foreach ($settings['columns'] as $type => $val):?>
										<?php	if ($val == FALSE) continue;
												if ($type == 'row_num' OR $type == 'id' OR $type == 'image') continue;
										?>
										<input rel="<?=$type?>" type="text" value="<?=$val?>" maxlength="256" onblur="if (value == '') {value='<?=$val?>'}" onfocus="if (value == '<?=$val?>') {value =''}">
										<?php endforeach;?>
									</div>
									<div class="right">
										<label><?=lang('ci:last')?></label>
										<select rel="limit"><option>50</option><option>75</option><option>100</option><option>150</option><option>200</option><option>500</option><option>1000</option><option>2500</option></select>
										<label><?=lang('ci:images')?></label>
									</div>
									<br clear="all">
								</div>
								<div class="images">
									<p class="Loading"><?=lang('ci:loading_images')?></p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<?php endif;?>
			</th>
		</tr>
		<tr class="ImageQueue hidden"><th colspan="99"></th></tr>
		<tr>
			<?php foreach ($settings['columns'] as $type => $val):?>
			<?php if ($val == FALSE) continue;?>
			<?php $size=''; if ($type == 'row_num') $size = '10'; elseif ($type == 'id') $size = '20'; elseif ($type == 'image') $size = '50';?>
			<th style="width:<?=$size?>px"><?=$val?></th>
			<?php endforeach;?>

			<th style="width:60px"><?=lang('ci:actions')?></th>
		</tr>
	</thead>
	<tbody class="AssignedImages">
	<?=$assigned_images?>
	<?php if ($total_images < 1):?><tr class="NoImages"><td colspan="99"><?=lang('ci:no_images')?></td></tr><?php endif;?>
	</tbody>
	<tfoot>
		<tr>
			<td <?php if ($settings['image_limit'] == '999999') echo 'style="display:none"';?> colspan="99" class="ImageLimit"><?=lang('ci:image_remain')?> <span class="remain"><?=$settings['image_limit']?></span></td>
		</tr>
	</tfoot>
</table>

	<input name="field_id_<?=$field_id?>[key]" type="hidden" value="<?=$temp_key?>"/>
	<input type="hidden" class="CI_Data" value='{"key":<?=$temp_key?>, "field_id":<?=$field_id?>, "site_id":<?=$site_id?>, "image_limit":<?=$settings['image_limit']?>, "jeditable_event": "<?=$settings['jeditable_event']?>", "mouseenter2edit": "<span><?=lang('ci:hover2edit')?></span>", "click2edit": "<span><?=lang('ci:click2edit')?></span>", "submitwait": "<?=lang('ci:submitwait')?>", "imglimitreached": "<?=lang('ci:img_limit_reached')?>"}'/>

	<div class="PerImageActionHolder hidden"><?=base64_encode($this->load->view('pbf_per_image_action', array(), TRUE))?></div>
</div>

<?php endif; ?>