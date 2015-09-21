<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-setting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-sizes" data-toggle="tab"><?php echo $tab_sizes; ?></a></li>
            <li><a href="#tab-addon" data-toggle="tab"><?php echo $tab_addon; ?></a></li>
            <li><a href="#tab-font" data-toggle="tab"><?php echo $tab_font; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-image-quality"><span data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_quality; ?>"><?php echo $entry_image_quality; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_image_quality]" id="input-image-quality" class="form-control">
                  <?php for ($i = 10; $i <= 100; $i += 5) { ?>
                    <option value="<?php echo $i;?>"<?php if ($i == $imagengine_image_quality || ($i == 80 && !$imagengine_image_quality)) echo ' selected="selected"';?>><?php echo $i;?></option>
				  <?php } ?>
				  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-popup-quality"><span data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_quality; ?>"><?php echo $entry_popup_quality; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_popup_quality]" id="input-popup-quality" class="form-control">
                  <?php for ($i = 10; $i <= 100; $i += 5) { ?>
                    <option value="<?php echo $i;?>"<?php if ($i == $imagengine_popup_quality || ($i == 80 && !$imagengine_popup_quality)) echo ' selected="selected"';?>><?php echo $i;?></option>
				  <?php } ?>
				  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-save-angle"><span data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_save_angle; ?>"><?php echo $entry_save_angle; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_save_angle]" id="input-save-angle" class="form-control">
                    <option value="0"<?php if (!$imagengine_save_angle) echo ' selected="selected"';?>><?php echo $text_once;?></option>
                    <option value="1"<?php if ($imagengine_save_angle) echo ' selected="selected"';?>><?php echo $text_each_angle;?></option>
				  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-cache"><span data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_cache; ?>"><?php echo $entry_cache; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_cache]" id="input-cache" class="form-control">
                    <option value="0"<?php if ($imagengine_cache === 0) echo ' selected="selected"';?>><?php echo $text_no;?></option>
                    <option value="1"<?php if ($imagengine_cache || $imagengine_cache === null) echo ' selected="selected"';?>><?php echo $text_yes;?></option>
				  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-clear-cache"><span data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_clear_cache; ?>"><?php echo $entry_clear_cache; ?></span></label>
                <div class="col-sm-10"><button class="btn btn-primary" onclick="clearcache(); return false;"><i class="fa fa-refresh"></i> <?php echo $entry_clear_cache; ?></button></div>
              </div>
            </div>
            <div class="tab-pane" id="tab-sizes">
              <?php foreach ($image_type as $img) { ?>
              <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-image-<?php echo $img;?>-width" style="width: 12.5%;"><?php echo ${'entry_image_' . $img}; ?></label>
                <div class="col-sm-11" style="width: 87.5%;">
                  <div class="row">
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_width; ?>"><i class="fa fa-arrows-h"></i></span>
						<input type="text" name="config[config_image_<?php echo $img;?>_width]" value="<?php echo ${'config_image_' . $img . '_width'}; ?>" placeholder="<?php echo $help_width; ?>" id="input-image-<?php echo $img;?>-width" class="form-control" />
                      </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_height; ?>"><i class="fa fa-arrows-v"></i></span>
						<input type="text" name="config[config_image_<?php echo $img;?>_height]" value="<?php echo ${'config_image_' . $img . '_height'}; ?>" placeholder="<?php echo $help_height; ?>" class="form-control" />
                      </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_fixed; ?>"><i class="fa fa-expand"></i></span>
                        <select name="imgne[imagengine_image_<?php echo $img;?>_fixed]" id="input-image-<?php echo $img;?>-fixed" class="form-control">
						  <option value="0"<?php if (!${'imagengine_image_' . $img . '_fixed'}) echo ' selected="selected"';?>><?php echo $text_no;?></option>
						  <option value="1"<?php if (${'imagengine_image_' . $img . '_fixed'} && ${'imagengine_image_' . $img . '_fixed'} != 'stretch') echo ' selected="selected"';?>><?php echo $text_yes;?></option>
						  <option value="stretch"<?php if (${'imagengine_image_' . $img . '_fixed'} == 'stretch') echo ' selected="selected"';?>><?php echo $text_stretch;?></option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_border; ?>"><i class="fa fa-square-o"></i></span>
                        <select name="imgne[imagengine_image_<?php echo $img;?>_border]" id="input-image-<?php echo $img;?>-border" class="form-control">
                        <?php for ($i = 0; $i <= 50; $i++) { ?>
						  <option value="<?php echo $i;?>"<?php if ($i == ${'imagengine_image_' . $img . '_border'} || (!$i && !${'imagengine_image_' . $img . '_border'})) echo ' selected="selected"';?>><?php echo $i;?></option>
                        <?php } ?>
				        </select>
                      </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_border_color; ?>"><i class="fa fa-square"></i></span>
						<input type="text" name="imgne[imagengine_image_<?php echo $img;?>_back]" value="<?php echo ${'imagengine_image_' . $img . '_back'}; ?>" id="input-image-<?php echo $img;?>-back" class="color {hash:false, required:false} form-control" />
                      </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_angle; ?>"><i class="fa fa-image fa-spin"></i></span>
                        <select name="imgne[imagengine_image_<?php echo $img;?>_angle]" id="input-image-<?php echo $img;?>-angle" class="form-control">
                        <?php for ($i = 0; $i <= 90; $i+=5) { ?>
						  <option value="<?php echo $i;?>"<?php if ($i == ${'imagengine_image_' . $img . '_angle'} || (!$i && !${'imagengine_image_' . $img . '_angle'})) echo ' selected="selected"';?>><?php echo $i;?></option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%;">
                      <div class="input-group">
                        <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-general" title="<?php echo $help_anglefix; ?>"><i class="fa fa-lock"></i></span>
                        <select name="imgne[imagengine_image_<?php echo $img;?>_anglefix]" id="input-image-<?php echo $img;?>-anglefix" class="form-control">
						  <option value="0"<?php if (!${'imagengine_image_' . $img . '_anglefix'}) echo ' selected="selected"';?>><?php echo $text_no;?></option>
						  <option value="1"<?php if (${'imagengine_image_' . $img . '_anglefix'}) echo ' selected="selected"';?>><?php echo $text_yes;?></option>
                        </select>
                      </div>
                    </div>
                  </div>
				  <?php if (${'error_image_' . $img}) { ?>
				  <div class="text-danger"><?php echo ${'error_image_' . $img}; ?></div>
                  <?php } ?>
                </div>
              </div>
			  <?php } ?>
            </div>
            <div class="tab-pane" id="tab-addon">
              <div class="form-group">
                <label class="col-sm-1 control-label" for="input-border-addon"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_border_addon; ?>"><?php echo $entry_border_addon; ?></span></label>
                <div class="col-sm-2"><a href="" id="thumb-border" data-toggle="image" class="img-thumbnail"><img src="<?php echo $border_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="imgne[imagengine_border_addon]" value="<?php echo $imagengine_border_addon; ?>" id="input-border-addon" />
                </div>
                <label class="col-sm-1 control-label" for="input-border-show"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_border_show; ?>"><?php echo $entry_border_show; ?></span></label>
                <div class="col-sm-2">
                  <div class="well well-sm" style="height: 100px; overflow: auto;">
                    <?php foreach ($border_shows as $border_show => $border_show_text) { ?>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="imgne[imagengine_border_show][]" value="<?php echo $border_show; ?>"<?php if (in_array($border_show, $imagengine_border_show)) echo ' checked="checked"';?> />
                        <?php echo $border_show_text; ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="input-group">
                    <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_border_fill; ?>"><?php echo $entry_border_fill; ?></span>
                    <input type="text" name="imgne[imagengine_border_fill]" value="<?php echo $imagengine_border_fill; ?>" id="input-border-fill" class="color {hash:false, required:false} form-control" />
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="input-group">
                    <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_border_scale; ?>"><?php echo $entry_border_scale; ?></span>
                    <input type="text" name="imgne[imagengine_border_scale]" value="<?php echo $imagengine_border_scale; ?>" id="input-border-scale" class="numeric form-control" />
                  </div>
                </div>
              </div>
              <fieldset>
                <legend><?php echo $text_image_addons; ?></legend>
                <div class="form-group">
                  <div class="col-sm-4">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $help_addon_padding; ?>"><?php echo $entry_addon_padding; ?></span>
                      <input type="text" name="imgne[imagengine_addon_padding]" value="<?php echo $imagengine_addon_padding; ?>" id="input-addon-padding" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $help_bestseller_limit; ?>"><?php echo $entry_bestseller_limit; ?></span>
                      <input type="text" name="config[config_bestseller_limit]" value="<?php echo $config_bestseller_limit; ?>" id="input-bestseller-limit" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $help_latest_limit; ?>"><?php echo $entry_latest_limit; ?></span>
                      <input type="text" name="config[config_latest_limit]" value="<?php echo $config_latest_limit; ?>" id="input-latest-limit" class="numeric form-control" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-1 control-label" for="input-specials-addon"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_specials_addon; ?>"><?php echo $entry_specials_addon; ?></span></label>
                  <div class="col-sm-2"><a href="" id="thumb-specials" data-toggle="image" class="img-thumbnail"><img src="<?php echo $specials_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                    <input type="hidden" name="imgne[imagengine_specials_addon]" value="<?php echo $imagengine_specials_addon; ?>" id="input-specials-addon" />
                  </div>
                  <label class="col-sm-1 control-label" for="input-specials-show"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_show; ?>"><?php echo $entry_show; ?></span></label>
                  <div class="col-sm-2">
                    <div class="well well-sm" style="height: 100px; overflow: auto;">
                      <?php foreach ($specials_shows as $specials_show => $specials_show_text) { ?>
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="imgne[imagengine_specials_show][]" value="<?php echo $specials_show; ?>"<?php if (in_array($specials_show, $imagengine_specials_show)) echo ' checked="checked"';?> />
                          <?php echo $specials_show_text; ?>
                        </label>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_x; ?>"><?php echo $entry_pos_x; ?></span>
                      <input type="text" name="imgne[imagengine_specials_x]" value="<?php echo $imagengine_specials_x; ?>" id="input-specials-x" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_y; ?>"><?php echo $entry_pos_y; ?></span>
                      <input type="text" name="imgne[imagengine_specials_y]" value="<?php echo $imagengine_specials_y; ?>" id="input-specials-y" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_scale; ?>"><?php echo $entry_scale; ?></span>
                      <input type="text" name="imgne[imagengine_specials_scale]" value="<?php echo $imagengine_specials_scale; ?>" id="input-specials-scale" class="numeric form-control" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-1 control-label" for="input-bestseller-addon"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_bestseller_addon; ?>"><?php echo $entry_bestseller_addon; ?></span></label>
                  <div class="col-sm-2"><a href="" id="thumb-bestseller" data-toggle="image" class="img-thumbnail"><img src="<?php echo $bestseller_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                    <input type="hidden" name="imgne[imagengine_bestseller_addon]" value="<?php echo $imagengine_bestseller_addon; ?>" id="input-bestseller-addon" />
                  </div>
                  <label class="col-sm-1 control-label" for="input-bestseller-show"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_show; ?>"><?php echo $entry_show; ?></span></label>
                  <div class="col-sm-2">
                    <div class="well well-sm" style="height: 100px; overflow: auto;">
                      <?php foreach ($bestseller_shows as $bestseller_show => $bestseller_show_text) { ?>
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="imgne[imagengine_bestseller_show][]" value="<?php echo $bestseller_show; ?>"<?php if (in_array($bestseller_show, $imagengine_bestseller_show)) echo ' checked="checked"';?> />
                          <?php echo $bestseller_show_text; ?>
                        </label>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_x; ?>"><?php echo $entry_pos_x; ?></span>
                      <input type="text" name="imgne[imagengine_bestseller_x]" value="<?php echo $imagengine_bestseller_x; ?>" id="input-bestseller-x" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_y; ?>"><?php echo $entry_pos_y; ?></span>
                      <input type="text" name="imgne[imagengine_bestseller_y]" value="<?php echo $imagengine_bestseller_y; ?>" id="input-bestseller-y" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_scale; ?>"><?php echo $entry_scale; ?></span>
                      <input type="text" name="imgne[imagengine_bestseller_scale]" value="<?php echo $imagengine_bestseller_scale; ?>" id="input-bestseller-scale" class="numeric form-control" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-1 control-label" for="input-latest-addon"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_latest_addon; ?>"><?php echo $entry_latest_addon; ?></span></label>
                  <div class="col-sm-2"><a href="" id="thumb-latest" data-toggle="image" class="img-thumbnail"><img src="<?php echo $latest_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                    <input type="hidden" name="imgne[imagengine_latest_addon]" value="<?php echo $imagengine_latest_addon; ?>" id="input-latest-addon" />
                  </div>
                  <label class="col-sm-1 control-label" for="input-latest-show"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_show; ?>"><?php echo $entry_show; ?></span></label>
                  <div class="col-sm-2">
                    <div class="well well-sm" style="height: 100px; overflow: auto;">
                      <?php foreach ($latest_shows as $latest_show => $latest_show_text) { ?>
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="imgne[imagengine_latest_show][]" value="<?php echo $latest_show; ?>"<?php if (in_array($latest_show, $imagengine_latest_show)) echo ' checked="checked"';?> />
                          <?php echo $latest_show_text; ?>
                        </label>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_x; ?>"><?php echo $entry_pos_x; ?></span>
                      <input type="text" name="imgne[imagengine_latest_x]" value="<?php echo $imagengine_latest_x; ?>" id="input-latest-x" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_y; ?>"><?php echo $entry_pos_y; ?></span>
                      <input type="text" name="imgne[imagengine_latest_y]" value="<?php echo $imagengine_latest_y; ?>" id="input-latest-y" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_scale; ?>"><?php echo $entry_scale; ?></span>
                      <input type="text" name="imgne[imagengine_latest_scale]" value="<?php echo $imagengine_latest_scale; ?>" id="input-latest-scale" class="numeric form-control" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-1 control-label" for="input-zoom-addon"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_zoom_addon; ?>"><?php echo $entry_zoom_addon; ?></span></label>
                  <div class="col-sm-2"><a href="" id="thumb-zoom" data-toggle="image" class="img-thumbnail"><img src="<?php echo $zoom_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                    <input type="hidden" name="imgne[imagengine_zoom_addon]" value="<?php echo $imagengine_zoom_addon; ?>" id="input-zoom-addon" />
                  </div>
                  <label class="col-sm-1 control-label" for="input-zoom-show"><span data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_show; ?>"><?php echo $entry_show; ?></span></label>
                  <div class="col-sm-2">
                    <div class="well well-sm" style="height: 100px; overflow: auto;">
                      <?php foreach ($zoom_shows as $zoom_show => $zoom_show_text) { ?>
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="imgne[imagengine_zoom_show][]" value="<?php echo $zoom_show; ?>"<?php if (in_array($zoom_show, $imagengine_zoom_show)) echo ' checked="checked"';?> />
                          <?php echo $zoom_show_text; ?>
                        </label>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_x; ?>"><?php echo $entry_pos_x; ?></span>
                      <input type="text" name="imgne[imagengine_zoom_x]" value="<?php echo $imagengine_zoom_x; ?>" id="input-zoom-x" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_pos_y; ?>"><?php echo $entry_pos_y; ?></span>
                      <input type="text" name="imgne[imagengine_zoom_y]" value="<?php echo $imagengine_zoom_y; ?>" id="input-zoom-y" class="numeric form-control" />
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <span class="input-group-addon" data-toggle="tooltip" data-container="#tab-addon" title="<?php echo $help_scale; ?>"><?php echo $entry_scale; ?></span>
                      <input type="text" name="imgne[imagengine_zoom_scale]" value="<?php echo $imagengine_zoom_scale; ?>" id="input-zoom-scale" class="numeric form-control" />
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
            <div class="tab-pane" id="tab-font">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-font-dir"><span data-toggle="tooltip" title="<?php echo $help_font_dir; ?>"><?php echo $entry_font_dir; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="imgne[imagengine_font_dir]" value="<?php echo $imagengine_font_dir; ?>" id="input-font-dir" class="autocomplete form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-font"><span data-toggle="tooltip" title="<?php echo $help_font; ?>"><?php echo $entry_font; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_font]" id="input-font" class="form-control" />
                  <?php foreach ($fonts as $font) { ?>
                    <option value="<?php echo $font;?>"<?php if ($imagengine_font == $font) echo ' selected="selected"';?>><?php echo $font; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-font-height"><span data-toggle="tooltip" title="<?php echo $help_font_height; ?>"><?php echo $entry_font_height; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_font_height]" id="input-font-height" class="form-control" />
                  <?php for ($i = 15; $i <= 150; $i += 5) { ?>
                    <option value="<?php echo $i;?>"<?php if ($imagengine_font_height == $i || (!$imagengine_font_height && $i == 40)) echo ' selected="selected"';?>><?php echo $i; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-font-color"><span data-toggle="tooltip" title="<?php echo $help_font_color; ?>"><?php echo $entry_font_color; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="imgne[imagengine_font_color]" value="<?php echo ($imagengine_font_color) ? $imagengine_font_color : '000000'; ?>" id="input-font-color" class="color {hash:false, required:true} form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-font-backg"><span data-toggle="tooltip" title="<?php echo $help_font_backg; ?>"><?php echo $entry_font_backg; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="imgne[imagengine_font_backg]" value="<?php echo $imagengine_font_backg; ?>" id="input-font-backg" class="color {hash:false, required:false} form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-font-size"><span data-toggle="tooltip" title="<?php echo $help_font_size; ?>"><?php echo $entry_font_size; ?></span></label>
                <div class="col-sm-10">
                  <select name="imgne[imagengine_font_size]" id="input-font-size" class="form-control" />
                  <?php for ($i = 10; $i <= 100; $i += 5) { ?>
                    <option value="<?php echo $i;?>"<?php if ($imagengine_font_size == $i || (!$imagengine_font_size && $i == 35)) echo ' selected="selected"';?>><?php echo $i; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function clearcache() {
	$.ajax({
		url: '<?php echo $clearcache;?>',
		dataType: 'json',
		success: function(json) {
			$('.warning, .success').remove();

			if (json['error']) {
				$('.breadcrumb').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				$('.alert-danger').fadeIn('slow');
				return false;
			}
			if (json['success']) {
				$('.breadcrumb').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				$('.alert-success').fadeIn('slow');
				return false;
			}
		}
	});
}

var cache = {};
$('.autocomplete').autocomplete({
	delay: 500,
	minLength: 0,
	source: function(request, response) {
		var term = request;
		if (term in cache) {
			$('#loadimg').fadeOut('slow', function() { $(this).remove(); });
			response(cache[term]);
			return;
		}
		$.ajax({
			url: '<?php echo $autocomplete;?>&dir=' + encodeURIComponent(term),
			dataType: 'json',
			success: function(json) {
				$('#loadimg').fadeOut('slow', function() { $(this).remove(); });
				response($.map(json, function(item) {
					cache[term] = {label: item, value: item};
					return {
						label: item,
						value: item
					};
				}));
			},
			error: function(xhr, ajaxOptions, thrownError) {
				$('#loadimg').fadeOut('slow', function() { $(this).remove(); });
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'select': function(item) {
		$(this).val(item['value']);
	}
});
$('.autocomplete').on('click', function() {
	$(this).closest('.form-group').find('label:first').append('&nbsp; <i id="loadimg" class="fa fa-spinner fa-spin" style="display:none;"></i>');
	$('#loadimg').fadeIn();
	$(this).autocomplete('search');
});

$('input.numeric').on('keyup', function() {
	var value = $(this).val().replace(/[^0-9-]/g, "");
	$(this).val(value);
});
//--></script> 
<?php echo $footer;