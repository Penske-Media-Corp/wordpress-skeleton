<?php
$git = '/usr/bin/git';
$themes_path = '/var/www/sites/example.com/wp-content/themes/vip/';
$log_path = '/var/www/sites/example.com/log'

$log = time() . "\t" . $_SERVER['REMOTE_ADDR'];
if ( ! isset( $_POST['payload'] ) || empty( $_POST['payload'] ) ) {
	$log .= "\t" . 'Invalid request' . PHP_EOL;
	$log .= "\t" . var_export( getallheaders(), true );
	file_put_contents ( $log_path, $log, FILE_APPEND );
	exit;
}

$payload = json_decode( $_POST['payload'] );

$log .= "\t" . var_export( $payload, true );
$log .= PHP_EOL;

$update = false;
if ( isset( $payload->commits ) && empty( $payload->commits ) ) {
	// When merging and pushing to bitbucket, the commits array will be empty.
	// In this case there is no way to know what branch was pushed to, so we will do an update.
	$update = true;
} elseif ( isset( $payload->commits ) && is_array( $payload->commits ) ) {
	foreach ( $payload->commits as $commit ) {
		$branch = $commit->branch;
		if ( $branch === 'master' || isset( $commit->branches ) && in_array( 'master', $commit->branches ) ) {
			$update = true;
			break;
		}
	}
}

$themes_path .= preg_replace( '~[^a-z0-9-]*~', '', $payload->repository->name );
if ( ! file_exists( $themes_path ) ) {
	$log .= "\t" . 'Directory does not exist: ' . $themes_path . PHP_EOL;
	file_put_contents ( $log_path, $log, FILE_APPEND );
	exit;
}

if ( $update ) {
	$log .= 'git clean -df && git checkout . && git pull' . PHP_EOL;
	$return = shell_exec( $git . ' --git-dir=' . $themes_path . '/.git --work-tree=' . $themes_path . ' clean -df');
	$log .= ( ! empty( $return ) ) ? $return . PHP_EOL : '';
	$return = shell_exec( $git . ' --git-dir=' . $themes_path . '/.git --work-tree=' . $themes_path . ' checkout .');
	$log .= ( ! empty( $return ) ) ? $return . PHP_EOL : '';
	$return = shell_exec( $git . ' --git-dir=' . $themes_path . '/.git --work-tree=' . $themes_path . ' pull' );
	$log .= ( ! empty( $return ) ) ? $return . PHP_EOL : '';
}

file_put_contents ( $log_path, $log, FILE_APPEND );

//EOF
