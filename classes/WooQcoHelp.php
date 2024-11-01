<?php
class WooQcoHelp{
	static function help(){
		?>
        <iframe scrolling="yes" src="<?php echo  plugins_url( 'documentation/index.html', dirname(__FILE__) ); ?>" style="width:100%;height: 1000px;"></iframe>
        <?php
	} //EOF
}
?>
