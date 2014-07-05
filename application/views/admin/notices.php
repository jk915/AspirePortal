<?php
        if( !empty( $message ) )
        {
?>
            <div class="message"><?php echo $message; ?></div>
<?php 
        }
?>

<?php
        if( !empty( $error ) )
        {
?>
            <div class="error"><?php echo $error; ?></div>
<?php 
        }
?>

<?php
        if( $this->session->flashdata( 'message' ) )
        {
?>
            <div class="message"><?php echo $this->session->flashdata( 'message' ); ?></div>
<?php 
        }
?>

<?php
        if( $this->session->flashdata( 'error' ) )
        {
?>
            <div class="error"><?php echo $this->session->flashdata( 'error' ); ?></div>
<?php 
        }
?>

<?php
        if( validation_errors() != '' )
        {
?>
            <div class="error"><?php echo validation_errors() ?></div>
<?php 
        }
?>

<?php
        if( !empty( $success ) )
        {
?>
            <div class="success"><?php echo $success; ?></div>
<?php 
        }
?>

<?php
        if( $this->session->flashdata( 'success' ) )
        {
?>
            <div class="success"><?php echo $this->session->flashdata( 'success' ); ?></div>
<?php 
        }
?>