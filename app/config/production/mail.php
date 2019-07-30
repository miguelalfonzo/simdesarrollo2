<?php

if( PRODUCCION )
{
	$pretend = false;
}
else
{
	$pretend = true;
}

return array(

	'pretend' => $pretend

);
