<div class="w_caption" style="background-color: #CCC; height: 23px;">
   <a href="javascript:void(0);" class="w_close"><!-- --></a><span class="w_captionText" id="_wicket_window_12"><b>Article Detail</b></span>
</div>

<!-- begin frmArticle -->
<form class="plain" id="frmArticle" name="frmArticle" method="post">
   <input type="hidden" id="article_id" name="article_id" value="<?=$article_id;?>" />
   
   <div style="text-align: left; padding-left: 50px; padding-right: 50px;" class="top-margin-sm">
      <div class="left" style="width: 350px;">
         <label for="article_title">Article Title:<span class="requiredindicator">*</span></label>
         <input type="text" id="article_title" name="article_title" class="required" value="<?=($article_id !="") ? $article->article_title : $article_title;?>" />
      </div>
      
      <div class="left left-margin" style="width: 350px;">
         <label for="article_date">Article Date:<span class="requiredindicator">*</span></label>
         <input type="text" id="article_date" name="article_date" class="required thin"  value="<?=($article_id !="") ? $article->article_date : "";?>" />      
      </div>
      
      <div class="clear"></div>

      <label for="short_description" class="top-margin-sm">Short Description:<span class="requiredindicator">*</span></label>
      <textarea id="short_description" cols="20" rows="7" name="short_description" class="editor" style="width: 742px; height: 80px;">
         <?=($article_id !="") ? $article->short_description : "";?>
      </textarea>
      
      <div class="clear"></div>        

      <label for="content" class="top-margin-sm">Content:</label>
      <textarea id="wysiwyg" cols="20" rows="10" name="content" class="editor" style="width:742px;height:110px"><?=($article_id !="") ? $article->content : "";?></textarea>        

      <div class="clear"></div>
      <div class="top-margin-sm"></div>
      
      <label for="download1">Article Attachment:<span class="requiredindicator">*</span></label>
      <input id="download1" name="download1" type="text" value="<?=($article_id !="") ? $article->download1 : "";?>" class="left" /> 
      <a class="btn-select-save sprite left left-margin" href="#"></a>     
      
      <div class="clear"></div> 
      <div class="top-margin-sm"></div> 
      <input id="enabled" type="checkbox" value="1" name="enabled" class="left" <? echo ($article_id !="") ? (($article->enabled == 1) ? "checked" :"") : "checked" ?> />
      <label class="left" style="padding-top: 0px;" for="enabled">&nbsp;Article is enabled</label>

      <div class="clear"></div>
      <input class="button top-margin-sm" type="button" value="Save" id="save_article" />
   </div>
</form>
<!-- end frmArticle -->