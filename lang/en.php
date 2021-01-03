<?php
class lang {
	const DATE = 'Дата';
	
	// Страница логина и уведомления
	const HDR_LOGIN = 'Welcome';
	const USERNAME = 'Username';
	const PASS = 'Password';
	const RESTORE_PASS = 'Restore password';
	const ERR_CAPTCHA = 'CAPTCHA не была пройдена!';
	const ERR_BLOCKED = 'This account is diasbled. Please contact your supervisor';
	const ERR_PASS = 'Wrong password';
	const ERR_PASS_DONT_MATCH = 'Passwords don\'t match';
	const ERR_NO_SUCH_USER = 'User doesn\'t exist';
	const ERR_NO_SUCH_EMAIL = 'User with such e-mail doesn\'t exist';
	const BTN_ENTER = 'Enter';
	
	
	
	// Имена ролей
	const LEVEL_BASIC = 'Мастер';
	const LEVEL_ADMIN = 'Администратор';
	const LEVEL_MULTIADMIN = 'Администратор+';
	const LEVEL_GODMODE = 'Полный доступ';
	const GODMODE_LOCATIONS = 'все';
	
	//TABS
	const TAB_ACTIVE = 'Активные';
	const TAB_ARCHIVE = 'Архив';
	
	// ФОРМЫ
	const SELECT_DEFAULT = 'Выберите';
	const OLD_PASS = 'Старый пароль';
	const NEW_PASS = 'Новый пароль';
	const CONFIRM_PASS = 'Подтвердите пароль';
	const HDR_NAME = 'Имя';
	const HDR_EMAIL = 'E-mail';
	const HDR_LOCATION = 'Салон';
	const HDR_LOCATION_PLURAL = 'Салоны';
	const HDR_ROLE = 'Уровень доступа';
	const HDR_CHANGE_PASS = 'Сменить пароль';
	const BTN_SHOW = 'Показать';
	const BTN_ADD = 'Добавить';
	const BTN_CHANGE = 'Изменить';
	
	//Кнопки управления
	const HDR_HANDLING = 'Управление';
	const HANDLING_CHANGE = 'Редактировать';
	const HANDLING_BLOCK = 'Заблокировать';
	const ALERT_BLOCK = 'Заблокировать пользователя ';
	const HANDLING_RESTORE = 'Восстановить';
	
	
	
	//Управление доступом к программе
	const HDR_ACCESS_LIST = 'Управление доступом';
	
	//HEAD
	const SEARCH_CLIENT_PLACEHOLDER = 'ФИО клиента';
	const LOGOUT = 'Выйти';
	
	
	//e-mail
	const EMAIL_SUBJECT = 'Восстановление пароля';
	const EMAIL_YOUR_DATA = 'Ваши данные для входа:';
	const EMAIL_OPTIONS = 'Вы можете изменить пароль в своем профиле';
	const EMAIL_WARNING = 'Если вы не отправляли запрос на восстановление пароля, проигнорируйте это письмо.';
	const EMAIL_SENT = 'Новый пароль отправлен на e-mail ';
	
	
	// Дни недели
	const MONDAY = 'пн.';
	const TUESDAY = 'вт.';
	const WEDNESDAY = 'ср.';
	const THURSDAY = 'чт.';
	const FRIDAY = 'пт.';
	const SATURDAY = 'сб.';
	const SUNDAY = 'вс.';
	
}

 ?>