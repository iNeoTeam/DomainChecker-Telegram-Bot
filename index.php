<?php
error_reporting(0);
set_time_limit(0);
ob_start();
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], 
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;
}
if(!$ok) die("Donbale chi migardi?! :)<br><br> <a href='https://ineo-team.ir'>iNeoTeam</a>");
include 'config.php';
define('API_KEY', $token);
# ===========================================
function iNeoTeamBot($method, $datas = []){
	$api = "https://api.telegram.org/bot".API_KEY."/".$method;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
	$res = curl_exec($ch);
	if(curl_error($ch)){
		var_dump(curl_error($ch));
	}else{
		return json_decode($res);
	}
}
function back2menu($data){
	$button = json_encode(['inline_keyboard' => [
	[['text' => "🔙برگشت به منو قبل", 'callback_data' => $data]],
	]]);
	return $button;
}
function step($chat_id, $data){
	file_put_contents("data/$chat_id/step.txt", $data);
}
function name($id){
	$name = file_get_contents("data/$id/name.txt");
	return $name;
}
function message($chat_id, $message, $web, $mode, $button){
	$m = iNeoTeamBot('sendMessage', [
		'chat_id' => $chat_id,
		'text' => $message,
		'disable_web_page_preview' => $web,
		'parse_mode' => $mode,
		'reply_markup' => $button,
	])->result;
	return $m->message_id;
}
function message2($chat_id, $message, $web, $mode, $button, $msgID){
	$m = iNeoTeamBot('sendMessage', [
		'chat_id' => $chat_id,
		'text' => $message,
		'disable_web_page_preview' => $web,
		'parse_mode' => $mode,
		'reply_markup' => $button,
		'reply_to_message_id' => $msgID,
	])->result;
	return $m->message_id;
}
function edit($chatID, $messageID, $message, $web, $mode, $button){
	iNeoTeamBot('editMessageText', [
		'chat_id' => $chatID,
		'message_id' => $messageID,
		'text' => $message,
		'disable_web_page_preview' => $web,
		'parse_mode' => $mode,
		'reply_markup' => $button,
	]);
}
function deleteMessage($chat_id, $message_id){
	iNeoTeamBot('deleteMessage', [
		'chat_id' => $chat_id,
		'message_id' => $message_id,
	]);
}
function Forward($to, $from, $wMSG){
	$m = iNeoTeamBot('forwardMessage', [
		'chat_id' => $to,
		'from_chat_id' => $from,
		'message_id' => $wMSG,
	])->result;
	return $m->message_id;
}
function isEnglish($str){
	return strlen($str) == mb_strlen($str,'utf-8');
}
# ===========================================
$update 				= json_decode(file_get_contents("php://input"));
$getMe					= iNeoTeamBot('getMe');
$bot					= $getMe->result->username;
$botname				= $getMe->result->first_name;
$botid					= $getMe->result->id;
$chat_id				= $update->message->chat->id;
$type					= $update->message->chat->type;
$first_name				= $update->message->chat->first_name;
$last_name				= $update->message->chat->last_name;
$username 				= $update->message->chat->username;
$message_id 			= $update->message->message_id;
$from_id				= $update->message->from->id;
$c_id					= $update->message->forward_from_chat->id;
$forward_id 			= $update->message->forward_from->id;
$forward_chat 			= $update->message->forward_from_chat;
$forward_chat_username	= $update->message->forward_from_chat->username;
$text 					= $update->message->text;
$_text 					= strtolower($update->message->text);
$inputType				= $update->message->entities[0]->type;
$callback_id 			= $update->callback_query->id;
$data 					= $update->callback_query->data;
$chatID 				= $update->callback_query->message->chat->id;
$messageID				= $update->callback_query->message->message_id;
$queryID 				= $update->inline_query->id;
$query 					= $update->inline_query->query;
$time	 				= json_decode(file_get_contents($api."/timezone.php?action=time&zone=fa"))->result->time;
$date	 				= json_decode(file_get_contents($api."/timezone.php?action=date&zone=fa"))->result->date;
$step					= file_get_contents("data/$chat_id/step.txt");
$step2					= file_get_contents("data/$chatID/step.txt");
$users					= explode("\n", file_get_contents("data/users.txt"));
$blocked				= explode("\n", file_get_contents("data/blocked.txt"));
mkdir("data");
mkdir("data/$chat_id");
$sign = "➖➖➖➖➖➖➖➖\n📣 @$channel";
$blockedMessage = "🖐سلام\n🌹با عرض پوزش!\n\n⛔️*دسترسی شما به این ربات قطع شد.*\n✅در صورتی که فکر میکنید به اشتباه از ربات مسدود شده اید، از طریق دکمه زیر، وارد [کانال پشتیبانی](https://t.me/ineosup/5) شده و به مدیریت پیام بدهید.\n$sign";
if(!file_exists("redirector.php")){
	file_put_contents("redirector.php", file_get_contents($apiAddr."/redirector.txt"));
	copy("redirector.php", "data/index.php");
}
if(!file_exists("data/$chat_id/index.php")){
	copy("redirector.php", "data/$chat_id/index.php");
}
$ineoteamButton = json_encode(['inline_keyboard' => [
[['text' => base64_decode('8J+To9qv2LHZiNmHINix2KjYp9iqINiz2KfYstuMINmIINiu2K/Zhdin2Kog2YXYrNin2LLbjCDYotuMINmG2KbZiA'), 'url' => base64_decode('aHR0cHM6Ly9ULm1lL2lOZW9UZWFt')]],
]]);
$homeButton = json_encode(['inline_keyboard' => [
[['text' => base64_decode('8J+To9qv2LHZiNmHINix2KjYp9iqINiz2KfYstuMINmIINiu2K/Zhdin2Kog2YXYrNin2LLbjCDYotuMINmG2KbZiA'), 'url' => base64_decode('aHR0cHM6Ly9ULm1lL2lOZW9UZWFt')]],
]]);
$homeButton2 = json_encode(['inline_keyboard' => [
[['text' => "🖥ورود به پنل مدیریت", 'callback_data' => "adminlogin"]],
[['text' => base64_decode('8J+To9qv2LHZiNmHINix2KjYp9iqINiz2KfYstuMINmIINiu2K/Zhdin2Kog2YXYrNin2LLbjCDYotuMINmG2KbZiA'), 'url' => base64_decode('aHR0cHM6Ly9ULm1lL2lOZW9UZWFt')]],
]]);
$blockedButton = json_encode(['inline_keyboard' => [
[['text' => "👤پشتیبانی تیم آی نئو", 'url' => "https://t.me/ineosup/5"]],
[['text' => "⚙️کانال سورس", 'url' => "https://t.me/ineosource"], ['text' => "📣کانال رسمی", 'url' => "https://t.me/$chnl"]],
]]);
if(file_exists("data/bot_offline.txt") && !in_array($chat_id, $admins) && !in_array($chatID, $admins)){
	$message = "💤*ربات در حال حاضر خاموش میباشد.*\n\n❗️این خاموشی ممکن است به دلیل آپدیت یا رفع مشکلات باشد که موقتی میباشد و گاهی اوقات ممکن است به صورت دائمی باشد.\n\n✅لطفا تا زمان روشن شدن مجدد، شکیبا باشید.\n$sign";
	if($chat_id != ""){
		step($chat_id, "none");
		message($chat_id, $message, true, "MarkDown", $ineoteamButton);
	}elseif($chatID != ""){
		step($chatID, "none");
		edit($chatID, $messageID, $message, true, "MarkDown", $ineoteamButton);
	}
	unlink("error_log");
	exit();
}
if(isset($chat_id) && in_array($chat_id, $blocked) && !in_array($chat_id, $admins) or isset($chatID) && in_array($chatID, $blocked) && !in_array($chatID, $admins)){
	if($chat_id != ""){
		step($chat_id, "none");
		message($chat_id, $blockedMessage, true, "MarkDown", $blockedButton);
	}elseif($chatID != ""){
		step($chatID, "none");
		edit($chatID, $messageID, $blockedMessage, true, "MarkDown", $blockedButton);
	}
	unlink("error_log");
	exit();
}
# ===========================================
if($_text == "/start"){
	step($chat_id, "none");
	if(!in_array($chat_id, $users)){
		$u = file_get_contents("data/users.txt");
		$u .= $chat_id."\n";
		file_put_contents("data/users.txt", $u);
	}
	file_put_contents("data/$chat_id/name.txt", str_replace($char, "", $first_name));
	$message = "🖐سلام <a href='tg://user?id=$chat_id'>".name($chat_id)."</a> عزیز.\n❤️به ربات جستجوگر دامنه خوش آمدید.\n➖➖➖➖➖➖➖➖\n✅با استفاده از این ربات، میتوانید دامنه های خالی را پیدا کنید.\n\n✏️نام کاربری دامنه مورد نظر خود را به صورت زیر ارسال کنید.\n🌐 <code>/search example</code>\n$sign";
	$button = $homeButton;
	if(in_array($chat_id, $admins)){
		$button = $homeButton2;
	}
	message($chat_id, $message, true, "HTML", $button);
}elseif(in_array($data, ['adminlogin', 'adminlogin2']) && !in_array($chatID, $admins)){
	step($chatID, "none");
	$message = "❗️تلاش برای ورود به پنل مدیریت توسط <a href='tg://user?id=$chatID'>".name($chatID)."</a> با شناسه کاربری <code>$chatID</code>\n$sign";
	message($admin, $message, true, "HTML", back2menu('adminlogin2'));
	$message = "❌شما دسترسی لازم برای این عملیات را ندارید.\n$sign";
	edit($chatID, $messageID, $message, true, "HTML", back2menu('home'));
}elseif(in_array($data, ['acti', 'bstatus', 'bon', 'boff', 'f2all', 's2all', 'unblockuser', 'blockuser']) && !in_array($chatID, $admins)){
	step($chatID, "none");
	$message = "❌شما دسترسی لازم برای این عملیات را ندارید.\n$sign";
	edit($chatID, $messageID, $message, true, "HTML", back2menu('home'));
}elseif(in_array($data, ['actiUpdate', 'acti']) && in_array($chatID, $admins)){
	step($chatID, "none");
	$n = "♻️آپدیت";
	if($data == "actiUpdate"){
		$n = "✅آپدیت شد";
		iNeoTeamBot('answercallbackquery', [
			'callback_query_id' => $callback_id,
			'text' => "✅فعالیت اخیر ربات با موفقیت آپدیت شد.",
			'show_alert' => false
		]);
	}
	$status = "روشن";
	$emoji = "✅";
	if(file_exists("data/bot_offline.txt")){
		$status = "خاموش";
		$emoji = "❌";
	}
	$ping = sys_getloadavg()[2];
	$usersCount = count($users) - 1;
	$blockedsCount = count($blocked) - 1;
	$adminsCount = count($admins);
	$ram = json_decode(file_get_contents($api."/byte.php?input=".memory_get_usage(true)))->result->result;
	$message = "📊<b>فعالیت اخیر ربات:</b> <code>$time - $date</code>

💡<b>وضعیت ربات:</b> <code>$status [$emoji]</code>
🌐<b>وضعیت پینگ سرور:</b> <code>$ping</code>
🖥<b>مقدار رم در حال استفاده:</b> <code>$ram</code>
⚙️<b>ورژن PHP ربات:</b> <code>".phpversion()."</code>
😎<b>تعداد مدیران:</b> <code>$adminsCount نفر</code>
👤<b>تعداد کاربران:</b> <code>$usersCount نفر</code>
⛔️<b>تعداد بلاک شده ها:</b> <code>$blockedsCount نفر</code>
$sign";
	$button = json_encode(['inline_keyboard' => [
	[['text' => $n, 'callback_data' => "actiUpdate"], ['text' => "🔙برگشت", 'callback_data' => "adminlogin"]],
	]]);
	edit($chatID, $messageID, $message, true, "HTML", $button);
}elseif(in_array($data, ['bstatus', 'bon', 'boff']) && in_array($chatID, $admins)){
	step($chatID, "none");
	if(file_exists("data/bot_offline.txt")){
		$status = "خاموش";
		$emoji = "❌";
	}else{
		$status = "روشن";
		$emoji = "✅";
	}
	if($data == "boff"){
		$status = "خاموش شد";
		$emoji = "❌";
		file_put_contents("data/bot_offline.txt", "success");
	}elseif($data == "bon"){
		$status = "روشن شد";
		$emoji = "✅";
		unlink("data/bot_offline.txt");
	}
	$message = "❤️<b>به منو وضعیت ربات خوش آمدید.</b>
➖➖➖➖➖➖➖➖
🌀<b>از دکمه های زیر استفاده کنید.</b>

🎫<b>وضعیت فعلی استفاده از ربات:</b> <code>[$emoji]$status</code>
$sign";
	$button = json_encode(['inline_keyboard' => [
	[['text' => "🌀وضعیت ربات: [$emoji]$status", 'callback_data' => "nothing"]],
	[['text' => "✅فعال کردن", 'callback_data' => "bon"], ['text' => "☑️غیرفعال کردن", 'callback_data' => "boff"]],
	[['text' => "♻️بررسی مجدد", 'callback_data' => "bstatus"], ['text' => "🔙برگشت", 'callback_data' => "adminlogin"]],
	]]);
	edit($chatID, $messageID, $message, true, "HTML", $button);
}elseif($data == "cancel"){
	step($chatID, "none");
	if(in_array($chatID, $admins)){
		$b = back2menu('adminlogin');
	}else{
		$b = back2menu('home');
	}
	edit($chatID, $messageID, "✅عملیات مورد نظر با موفقیت لغو شد.\n$sign", true, "HTML", $b);
}elseif(in_array($data, ['s2all', 'f2all']) && in_array($chatID, $admins)){
	$c = count($users) - 1;
	if($data == 'f2all'){
		step($chatID, "forward2all");
		$action = "فوروارد";
	}else{
		step($chatID, "send2all");
		$action = "ارسال";
	}
	$message = "📝پیام خود را جهت $action به `$c` کاربر، $action کنید.\n$sign";
	$button = json_encode(['inline_keyboard' => [
	[['text' => "❌لغو عملیات", 'callback_data' => "cancel"]],
	]]);
	edit($chatID, $messageID, $message, true, "MarkDown", $button);
}elseif(isset($update->message->text) && $step == "send2all"){
	step($chat_id, "none");
	$text = str_replace($char, "", $update->message->text);
	$msgID = message($chat_id, "♻️در حال ارسال پیام به کاربران ...\n$sign", true, "MarkDown", $ineoteamButton);
	$_message = "📝<b>پیام همگانی از طرف پشتیبانی:</b>\n\n💬<b>متن پیام:</b> <code>$text</code>\n$sign";
	$members = fopen("data/users.txt", 'r');
	while(!feof($members)){
		$user = fgets($members);
		message($user, $_message, true, "HTML", $ineoteamButton);
	}
	deleteMessage($chat_id, $msgID);
	message($chat_id, "✅پیام با موفقیت برای تمام کاربران ارسال شد.\n$sign", true, "MarkDown", back2menu('adminlogin2'));
}elseif(isset($update->message) && $step == "forward2all"){
	step($chat_id, "none");
	$text = str_replace($char, "", $update->message->text);
	$msgID = message($chat_id, "♻️در حال فوروارد پیام به کاربران ...\n$sign", true, "MarkDown", $ineoteamButton);
	$members = fopen("data/users.txt", 'r');
	while(!feof($members)){
		$user = fgets($members);
		Forward($user, $chat_id, $message_id);
	}
	deleteMessage($chat_id, $msgID);
	message($chat_id, "✅پیام با موفقیت برای تمام کاربران فوروارد شد.\n$sign", true, "MarkDown", back2menu('adminlogin2'));
}elseif(in_array($data, ['unblockuser', 'blockuser']) && in_array($chatID, $admins)){
	if($data == 'blockuser'){
		step($chatID, "blockuser");
		$action = "بلاک";
	}else{
		step($chatID, "unblockuser");
		$action = "آنبلاک";
	}
	$message = "🆔شناسه کاربری شخص مورد نظر را جهت $action کردن ارسال کنید.\n$sign";
	$button = json_encode(['inline_keyboard' => [
	[['text' => "❌لغو عملیات", 'callback_data' => "cancel"]],
	]]);
	edit($chatID, $messageID, $message, true, "MarkDown", $button);
}elseif(isset($update->message->text) && $step == "unblockuser"){
	step($chat_id, "none");
	$id = str_replace($char, "", $text);
	if(!in_array($id, $users)){
		$message = "❌کاربری با شناسه کاربری `$id` در دیتابیس ربات پیدا نشده است.\n$sign";
		message($chat_id, $message, true, "MarkDown", back2menu('adminlogin2'));
		exit();
	}
	if(in_array($id, $admins)){
		$message = "❌ادمین های ربات بلاک نمیشوند که میخواهید آنبلاک کنید.\n$sign";
		message($chat_id, $message, true, "MarkDown", back2menu('adminlogin2'));
		exit();
	}
	if(!in_array($id, $blocked)){
		$message = "❌کاربری با شناسه کاربری `$id` در لیست بلاک شده ها پیدا نشد.\n$sign";
	}else{
		$message = "✅حساب کاربر مورد نظر با شناسه کاربری `$id` آنبلاک شد.\n$sign";
		$blockeds = file_get_contents("data/blocked.txt");
		$blockeds = str_replace("$id\n", "", $blockeds);
		file_put_contents("data/blocked.txt", $blockeds);
		message($id, "✅حساب شما توسط پشتیبانی آنبلاک شد\n$sign", true, "MarkDown", back2menu('home2'));
	}
	message($chat_id, $message, true, "MarkDown", back2menu('adminlogin2'));
}elseif(isset($update->message->text) && $step == "blockuser"){
	step($chat_id, "none");
	$id = str_replace($char, "", $text);
	if(!in_array($id, $users)){
		$message = "❌کاربری با شناسه کاربری `$id` در دیتابیس ربات پیدا نشده است.\n$sign";
		message($chat_id, $message, true, "MarkDown", back2menu('adminlogin2'));
		exit();
	}
	if(in_array($id, $admins)){
		$message = "❌ادمین های ربات را نمیتوانید بلاک کنید.\n$sign";
		message($chat_id, $message, true, "MarkDown", back2menu('adminlogin2'));
		exit();
	}
	if(!in_array($id, $blocked)){
		$message = "✅حساب کاربر مورد نظر با شناسه کاربری `$id` بلاک شد.\n$sign";
		$blockeds = file_get_contents("data/blocked.txt");
		$blockeds .= $id."\n";
		file_put_contents("data/blocked.txt", $blockeds);
		message($id, "❌حساب شما توسط پشتیبانی بلاک شد.\n$sign", true, "MarkDown", $ineoteamButton);
	}else{
		$message = "❌کاربر مورد نظر با شناسه کاربری `$id` از قبل در لیست بلاک شده ها بوده است.\n$sign";
	}
	message($chat_id, $message, true, "MarkDown", back2menu('adminlogin2'));
}elseif(in_array($data, ['adminlogin', 'adminlogin2']) && in_array($chatID, $admins)){
	step($chatID, "none");
	$message = "🖐با سلام [مدیر](tg://user?id=$chatID) گرامی!\n❤️به پنل مدیریت ربات خوش آمدید.\n➖➖➖➖➖➖➖➖\n✅از گزینه های زیر جهت مدیریت ربات استفاده کنید.\n$sign";
	$button = json_encode(['inline_keyboard' => [
	[['text' => "🤖وضعیت ربات", 'callback_data' => "bstatus"], ['text' => "📊فعالیت اخیر", 'callback_data' => "acti"]],
	[['text' => "💬ارسال همگانی", 'callback_data' => "s2all"], ['text' => "⏩فوروارد هنگانی", 'callback_data' => "f2all"]],
	[['text' => "✅آنبلاک کردن", 'callback_data' => "unblockuser"], ['text' => "❌بلاک کردن", 'callback_data' => "blockuser"]],
	[['text' => "🔙برگشت به منو اصلی", 'callback_data' => "home"]],
	]]);
	if($data == "adminlogin"){
		edit($chatID, $messageID, $message, true, "MarkDown", $button);
	}else{
		message2($chatID, $message, true, "MarkDown", $button, $messageID);
	}
}elseif(in_array($data, ['home', 'home2'])){
	step($chatID, "none");
	$message = "🖐سلام <a href='tg://user?id=$chat_id'>".name($chatID)."</a> عزیز.\n❤️به ربات جستجوگر دامنه خوش آمدید.\n➖➖➖➖➖➖➖➖\n✅با استفاده از این ربات، میتوانید دامنه های خالی را پیدا کنید.\n\n✏️نام کاربری دامنه مورد نظر خود را به صورت زیر ارسال کنید.\n🌐 <code>/search example</code>\n$sign";
	$button = $homeButton;
	if(in_array($chatID, $admins)){
		$button = $homeButton2;
	}
	if($data == "home"){
		edit($chatID, $messageID, $message, true, "HTML", $button);
	}else{
		message2($chatID, $message, true, "HTML", $button, $messageID);
	}
}elseif($_text == "/search"){
	step($chat_id, "none");
	$message = "❌<b>خطایی رخ داده است!</b>\n\n✅دستور را مانند مثال زیر ارسال کنید.\n🌐 <code>/search example</code>\n$sign";
	message2($chat_id, $message, true, "HTML", back2menu('home2'), $message_id);
}elseif(strpos($_text, "/search ") !== false){
	step($chat_id, "none");
	$typeEN = array('domain already registered.', 'domain is available.');
	$typeFA = array('دامنه از قبل ثبت شده است.', 'دامنه قابل ثبت است.');
	$input = str_replace($char, "", $_text);
	$domainUser = str_replace("/search ", "", $input);
	$domainUser = str_replace(" ", "", $domainUser);
	$m = message($chat_id, "♻️لطفا کمی صبر کنید ...\n\n❗️در حال دریافت اطلاعات از دامنه.\n\n⚠️*نکته مهم:* `این عملیات ممکن است کمی زمان بر باشد.`\n$sign", true, "MarkDown", $ineoteamButton);
	if(isEnglish($domainUser) != 1){
		$message = "❌شما از کاراکترهای فارسی نمیتوانید استفاده کنید.\n$sign";
		message2($chat_id, $message, true, "HTML", back2menu('home2'), $message_id);
		deleteMessage($chat_id, $m);
		exit();
	}
	$get = json_decode(file_get_contents($api."/domainChecker.php?domain=".$domainUser));
	if($get->status != "successfully."){
		$message = "❌خطایی رخ داده است.\n\n⚠️*علت خطا:* `".$get->status."`\n$sign";
		message2($chat_id, $message, true, "MarkDown", back2menu('home2'), $message_id);
		deleteMessage($chat_id, $m);
		exit();
	}
	$ir = $get->result->ir;
	$statusIR = str_replace($typeEN, $typeFA, $ir->status->type);
	if($ir->status->type == 'domain is available.'){
		$sellIR = "\n<a href='".$seller.$ir->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	$com = $get->result->com;
	$statusCOM = str_replace($typeEN, $typeFA, $com->status->type);
	if($com->status->type == 'domain is available.'){
		$sellCOM = "\n<a href='".$seller.$com->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	$org = $get->result->org;
	$statusORG = str_replace($typeEN, $typeFA, $org->status->type);
	if($org->status->type == 'domain is available.'){
		$sellORG = "\n<a href='".$seller.$org->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	$net = $get->result->net;
	$statusNET = str_replace($typeEN, $typeFA, $net->status->type);
	if($net->status->type == 'domain is available.'){
		$sellNET = "\n<a href='".$seller.$net->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	$info = $get->result->info;
	$statusINFO = str_replace($typeEN, $typeFA, $info->status->type);
	if($info->status->type == 'domain is available.'){
		$sellINFO = "\n<a href='".$seller.$info->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	$co = $get->result->co;
	$statusCO = str_replace($typeEN, $typeFA, $co->status->type);
	if($co->status->type == 'domain is available.'){
		$sellCO = "\n<a href='".$seller.$co->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	$ge = $get->result->ge;
	$statusGE = str_replace($typeEN, $typeFA, $ge->status->type);
	if($ge->status->type == 'domain is available.'){
		$sellGE = "\n<a href='".$seller.$ge->domain."'>🛒خرید آنلاین این دامنه</a>";
	}
	if($ir->domain == ""){
		$message = "❌خطایی رخ داده است.\n\n⚠️*پارامتر ورودی را بررسی کنید.*\n$sign";
		message2($chat_id, $message, true, "MarkDown", back2menu('home2'), $message_id);
		deleteMessage($chat_id, $m);
		exit();
	}
	deleteMessage($chat_id, $m);
	$message = "🔎<b>جستجو برای:</b> <code>$domainUser</code>\n\n🌐<b>آدرس دامنه:</b> ".$ir->domain."\n<b>🌀وضعیت:</b> <code>$statusIR [".$ir->status->emoji."]</code>$sellIR\n\n🌐<b>آدرس دامنه:</b> ".$com->domain."\n<b>🌀وضعیت:</b> <code>$statusCOM [".$com->status->emoji."]</code>$sellCOM\n\n🌐<b>آدرس دامنه:</b> ".$org->domain."\n<b>🌀وضعیت:</b> <code>$statusORG [".$org->status->emoji."]</code>$sellORG\n\n🌐<b>آدرس دامنه:</b> ".$net->domain."\n<b>🌀وضعیت:</b> <code>$statusNET [".$net->status->emoji."]</code>$sellNET\n\n🌐<b>آدرس دامنه:</b> ".$info->domain."\n<b>🌀وضعیت:</b> <code>$statusINFO [".$info->status->emoji."]</code>$sellINFO\n\n🌐<b>آدرس دامنه:</b> ".$co->domain."\n<b>🌀وضعیت:</b> <code>$statusCO [".$co->status->emoji."]</code>$sellCO\n\n🌐<b>آدرس دامنه:</b> ".$ge->domain."\n<b>🌀وضعیت:</b> <code>$statusGE [".$ge->status->emoji."]</code>$sellGE\n\n💥<b>قدرت گرفته توسط</b> <a href='https://t.me/ineoteam'>آی نئو</a>\n$sign";
	message2($chat_id, $message, true, "HTML", back2menu('home2'), $message_id);
}
unlink("error_log");
?>
