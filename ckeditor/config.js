/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.config.forcePasteAsPlainText = true;

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    
    // add custom plugins
    config.extraPlugins = 'new_block,new_video,wordcount';
        
    config.toolbar = 'CMS';

    config.toolbar_CMS =
    [
        ['Source','-','NewPage','Preview','-','Templates'],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],'-',['Styles','Format'], 
        '/',
        ['Bold','Italic'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Anchor'],
        ['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak'],
        ['Maximize', 'ShowBlocks','-','About','new_block','new_video']
    ];
    
};
