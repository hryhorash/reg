<?php
class lang {
	const DATE = 'Дата';
	
	// Страница логина и уведомления
	const TITLE_LOGIN = 'Авторизация';
	const HDR_LOGIN = 'Представьтесь пожалуйста';
	const USERNAME = 'Логин';
	const PASS = 'Пароль';
	const H2_RESTORE_PASS = 'Восстановление пароля';
	const HDR_RESTORE_EMAIL = 'Укажите e-mail';
	const RESTORE_PASS = 'Восстановить пароль';
	const LANGUAGE = 'Язык интерфейса';
	const ERR_CAPTCHA = 'CAPTCHA не была пройдена!';
	const ERR_BLOCKED = 'Аккаунт заблокирован. Пожалуйста, обратитесь к администратору';
	const ERR_PASS = 'Пароль введен неверно';
	const ERR_PASS_DONT_MATCH = 'Пароли не совпадают';
	const ERR_NO_SUCH_USER = 'Такого пользователя не существует';
	const ERR_NO_SUCH_EMAIL = 'Пользователя с таким e-mail не существует';
	const ERR_NO_RIGHTS = 'Недостаточно прав';
	const ERR_NO_WAY = 'Удаление невозможно';
	const ERR_NO_ID = 'Не указан идентификатор';
	const ERR_NO_CLIENT = 'Не указан клиент';
	const ERR_NO_STAFF = 'Укажите ответственного сотрудника';
	const ERR_NO_INFO = 'Нет данных для отображения';
	const ERR_SELECT_LOCATION = 'Необходимо выбрать хотя бы один салон из списка';
	const ERR_SELECT_SPECIALTY = 'Выберите хотя бы один вид работ';
	const ERR_NO_PRICE = 'Необходимо указать стоимость';
	const ERR_NO_RECOMMENDATION = 'Клиент по рекомендации указан неверно';
	const ERR_NO_GENDER = 'Необходимо указать пол клиента';
	const ERR_CONSTRAINT = 'Удаление невозможно';
	const ERR_SPECIFY_DAY = 'Необходимо указать день';
	const ERR_PHONE = 'Напишите телефон в формате 380ххххххххх пожалуйста';
	const ERR_GENERAL = 'Произошла ошибка';
	const SUCCESS_PASS = 'Пароль изменен';
	const SUCCESS_DELETE = 'Запись удалена';
	const SUCCESS_RESTORE = 'Доступ восстановлен';
	const SUCCESS_GENERAL = 'Изменения сохранены';
	const SUCCESS_GENERAL_ADD = 'Запись добавлена';
	const SUCCESS_USER_BLOCKED = 'Пользователь заблокирован';
	const SUCCESS_NEW_VISIT = 'Спасибо! Ваша заявка принята. <br />Ожидайте подтверждения';
	const BTN_ENTER = 'Войти';
	const BTN_DONE = 'Готово';
	const BTN_CANCEL = 'Отмена';
	const BTN_CLIENT_VISIT = 'Записаться';
	const MSG_SOLD = '. Продано ';
	const HDR_AVG_DURATION = 'Средняя длительность';
	const HDR_RATE_PLACEHOLDER = 'cтавка';
	const H2_GOODS_TO_SELL = 'Косметика на продажу в';
	const H2_WORK_COSM_AVAILABLE = 'Баланс расходной косметики в';
	const HDR_AVAILABILITY = 'Наличие';
		const HDR_PCS = 'ед.';
		const HDR_GR = 'гр.';
	const HDR_COST_PER_GR = 'Цена за гр.';
	
	
	// Имена ролей
	const LEVEL_BASIC = 'Мастер';
	const LEVEL_ADMIN = 'Администратор';
	const LEVEL_GENERAL = 'Управляющий';
	const LEVEL_GODMODE = 'Полный доступ';
	const GODMODE_LOCATIONS = 'все';
	
	//TABS
	const TAB_ACTIVE = 'Актуальные';
	const TAB_ACTIVE_M = 'Актуальный';
	const TAB_ACTIVE_F = 'Активная';
	const TAB_ARCHIVE = 'архив';
	const TAB_BRANDS = 'Бренды';
	const TAB_COSMETICS = 'косметика';
	const TAB_SUPPLIERS = 'Поставщики';
	const TAB_ALL = 'все';
	const TAB_DAYS_OFF = 'Выходные';
	const TAB_WORKDAYS = 'График работы';
	const TAB_LOCATIONS = 'Локации';
	const TAB_USERS = 'Сотрудники';
	const TAB_LIST	= 'cписок';
	const TAB_IN_PROGRESS	= 'в работе';
	const TAB_ADD	= 'добавить';
	const TAB_EXTRA	= 'сопутствующие расходы';
	const TAB_WORKDAYS_USERS = 'График работы сотрудников';
	const TAB_PLANS = 'планы';
	const TAB_SELL = 'продать!';
	
	//SIDEBAR
	const SIDEBAR_FILTERS = 'Фильтр:';
	
	// ФОРМЫ
	const SELECT_DEFAULT = 'Выберите';
	const SELECT_ALL = 'Показать все';
	const SELECT_ADD_ALL = ' (все)';
	const OLD_PASS = 'Старый пароль';
	const NEW_PASS = 'Новый пароль';
	const CONFIRM_PASS = 'Подтвердите пароль';
	const HDR_NAME = 'Имя';
	const HDR_EMAIL = 'E-mail';
	const HDR_CITY = 'Город';
	const HDR_LOCATION = 'Салон';
	const HDR_LOCATION_PLURAL = 'Салоны';
	const HDR_OPERATING_HOURS = 'Часы работы';
	const HDR_OPEN_FROM = ' c';
	const HDR_OPEN_TILL = ' до';
	const HDR_WORKTYPE_CAT = 'Вид услуг';
	const HDR_WORKTYPE_CATS = 'Виды услуг';
	const H2_WORKTYPE_CAT = 'Новый вид услуг';
	const H2_WORKTYPE_NEW = 'Новая услуга';
	const HDR_WORKTYPE = 'Услуга';
	const HDR_ITEM_NAME = 'Наименование';
	const HDR_ENTITY_NAME = 'Название';
	const CHANGE_ITEM_NAME = 'Изменить наименование';
	const HDR_WORKTYPE_TARGET = 'Для кого';
	const HDR_PRICE = 'Цена';
	const HDR_WORKTYPE_PRICE_RANGE = 'Диапазон цен';
	const HDR_WORKTYPE_MINPRICE = 'Цена от';
	const HDR_WORKTYPE_MAXPRICE = 'Цена до';
	const HDR_ACTIVE_FROM = 'Действует с';
	const HDR_COST = 'Стоимость';
	const PER_PIECE_PLACEHOLDER = 'в день';
	const PER_MONTH_PLACEHOLDER = 'или в месяц';
	const PHONE_PLACEHOLDER = 'телефон';
	const PER_MONTH = ' в месяц'; //пробел в начале нужен!
	const HDR_MONTH = 'Месяц'; 
	const HDR_TOTAL = 'Итого'; 
	const HDR_BRAND = 'Бренд '; 
	const HDR_RRP = 'Рекомендуемая цена'; 
	const HDR_DESCRIPTION = 'Описание'; 
	const HDR_CONTACTS = 'Контакты'; 
	const HDR_NEW_DAY_OFF = 'Новый выходной'; 
	const HDR_WORKDAY = 'Рабочие дни'; 
	const HDR_NEW_WORKDAY = 'Новый рабочий день'; 
	const HDR_TIME = 'Время (c/по)'; 
	const HDR_TIME_FROM = 'Время начала'; 
	const HDR_TIME_TO = 'Время окончания'; 
	const HDR_VISIT_DURATION = 'Продолжительность'; 
	const HDR_TOTAL_PRICE_RANGE = 'Итоговый диапазон цен'; 
	const HDR_TO_PAY = 'К оплате'; 
	const HDR_VISIT_DATA = 'Данные визита'; 
	const HDR_WORKTYPE_LIST = 'Заказанные услуги'; 
	const HDR_NETTO_SEVICES = 'Сопутствующие услуги'; 
	const HDR_SPENT_LIST = 'Расходная косметика'; 
	const HDR_SALES_LIST = 'Продажи'; 
	const HDR_SPENT_VOLUME = 'гр.'; 
	const HDR_COST_PER_EMPLOYEE = 'Сотрудники и стоимость'; 
	const HDR_TIPS = 'Чай'; 
	const HDR_VISIT_TOTALS = 'Итоги визита'; 
	const HDR_CUSTOMER_SERVICE = 'Обслуживание клиента'; 
	const HDR_NETTO = 'Нетто'; 
	const HDR_PROFIT = 'Прибыль'; 
	const HDR_WAGE = 'Зарплата'; 
	const HDR_SERVICE_NETTO = 'Себестоимость услуг'; 
	const HDR_ADD_SERVICE_NETTO = 'Добавить себестоимость услуги'; 
	const HDR_SERVICES_NETTO = 'Затраты нетто на:'; 
	const MENU_WAREHOUSE = 'Склад'; 
	const HDR_SERVICES = 'Услуги'; 
	const HDR_FORMULA = 'Формула'; 
	const HDR_SALE_ARCHIVE = 'Архив продаж'; 
	
	
	const HDR_CATEGORY = 'Категория';
	const HDR_SUBCATEGORY = 'Подкатегория';
	const HDR_SHOW_IN_MENU = 'Отображение в меню';
	
	const HDR_ROLE = 'Уровень доступа';
	const HDR_CHANGE_PASS = 'Сменить пароль';
	const BTN_SHOW = 'Показать';
	const BTN_ADD = 'Добавить';
	const BTN_ADD_WORK = 'Добавить услугу';
	const BTN_ADD_SPENT = 'Добавить расход';
	const BTN_ADD_EMPLOYEE = 'Добавить сотрудника';
	const BTN_ADD_SALE = 'Добавить продажу';
	const BTN_SELL = 'Продать';
	const BTN_CHANGE = 'Изменить';
	const BTN_NEXT = 'Далее';
	const BTN_SAVE = 'Сохранить изменения';
	
	//Кнопки управления
	const HDR_HANDLING = 'Управление';
	const HANDLING_VIEW = 'Детальнее';
	const HANDLING_CHANGE = 'Редактировать';
	const HANDLING_BLOCK = 'Заблокировать';
	const HANDLING_ARCHIVE = 'Перенести в архив';
	const HANDLING_APPROVE = 'Визит состоялся';
	const HANDLING_NOSHOW = 'Визит не состоялся. No show';
	const HANDLING_DELETE = 'Удалить';
	const ALERT_BLOCK_DEFAULT = 'Переместить в архив ';	//пробел в конце нужен
	const ALERT_BLOCK = 'Заблокировать пользователя ';	//пробел в конце нужен
	const ALERT_BLOCK_LOCATION = 'Отправить в архив ';
	const ALERT_DELETE = 'Внимание! Операция не может быть отменена\nУдалить ';	//пробел в конце нужен
	const ALERT_CONFIRM_VISIT = 'Отметить как состоявшийся без изменений?';
	const ALERT_NOSHOW = 'Клиент не пришел?';
	const ALERT_RESTORE_LOCATION = 'Активировать локацию ';
	const HANDLING_RESTORE = 'Восстановить '; 			//пробел в конце нужен
	const ALERT_WRONG_DATE_STATE = 'Этот статус недоступен для дат в будущем';
	const ALERT_EXCEED_LIMIT = 'Для увеличения количества нажмите "' . lang::BTN_ADD_SALE . '"';
	const HDR_CALENDAR_TAKEN = 'занято';
	
	
	//Управление доступом к программе
	const HDR_ACCESS_LIST = 'Сотрудники';
	const HDR_ACCESS_EDIT = 'Права доступа для ';
	const HDR_NEW_USER = 'Новый пользователь';
	const NAME = 'Имя';
	const SURNAME = 'Фамилия';
	const HDR_PHONE = 'Телефон';
	const HDR_PHONES = 'Телефоны';
	const PHONE_PLACEHOLDER_PATTERN = '380xxxxxxxxx';
	const HDR_COMMENT = 'Комментарий';
	const COMMENT_PLACEHOLDER = 'Комментарий';
	const HDR_RECOMMENDATION = 'По рекомендации';
	const HDR_GENDER = 'Пол';
		const HDR_MALE = 'мужской';
		const HDR_FEMALE = 'женский';
	
	const H2_NEW_CLIENT = 'Новый клиент';
	const TBL_CLIENT = 'Клиент';
	const HDR_PROMPT = 'Подсказка';
	const HDR_CLIENT_SOURCE = 'Источник';
	const HDR_DOB = 'День рождения';
	const HDR_VOLUME = 'Ёмкость';
	const HDR_PURPOSE = 'Назначение';
		const HDR_PURPOSE_WORK = 'в работу';
		const HDR_PURPOSE_SALE = 'на продажу';
		const HDR_PURPOSE_BOTH = 'универсальная';
		const HDR_PURPOSE_ACCOUNT = 'учёт';
	const HDR_ARTICUL = 'Артикул';
	const HDR_CONTACT_NAME = 'Контактное лицо';
	const HDR_CONTACT_POSITION = 'Должность';
	const HDR_SITE = 'Сайт';
	const HDR_ADDRESS = 'Адрес';
	const HDR_PURCHASE_STATE = 'Статус заказа';
		const PURCHASE_STATE0 = 'формируется';
		const PURCHASE_STATE1 = 'отправлен поставщику';
		const PURCHASE_STATE2 = 'счет получен';
		const PURCHASE_STATE3 = 'счет оплачен';
		const PURCHASE_STATE4 = 'заказ получен';
		const PURCHASE_STATE5 = 'заказ получен и оплачен';
	const HDR_EXPIRE = 'Срок годности';
	const HDR_DATE_PAID = 'Дата оплаты';
	const HDR_DATE_RECEIVED = 'Дата получения';
	const NO_NAME = 'Без названия';
	const HDR_EMPLOYEE = 'Сотрудник';
	
	const EXPL_NETTO_PRICE = 'Суммы указываются справочно. Данные не учитываются в фин.отчетах';
	const EXPL_WORK_PRICE = 'Сумма указывается только если нужно высчитать % зарплаты сотрудника';
	const EXPL_PRICE_PER_GR = 'Ориентировочно. За основу берется самая дорогая единица товара из закупки';
	
	
	//HEAD
	const SEARCH_CLIENT_PLACEHOLDER = 'ФИО клиента';
	const LOGOUT = 'Выйти';
	
	
	//e-mail
	const EMAIL_SUBJECT = 'Восстановление пароля';
	const EMAIL_YOUR_DATA = 'Ваши данные для входа:';
	const EMAIL_OPTIONS = 'Вы можете изменить пароль в своем профиле';
	const EMAIL_WARNING = 'Если вы не отправляли запрос на восстановление пароля, проигнорируйте это письмо.';
	const EMAIL_SENT = 'Новый пароль отправлен на e-mail ';
	
	
	// Опции для видов услуг
	const TARGET_MALE = 'для мужчин';
	const TARGET_FEMALE = 'для женщин';
	const TARGET_CHILD = 'для детей';
	const TARGET_GENERAL = 'универсальная';
	
	const HDR_VAT = 'Цены';
		const VAT_NO = 'без НДС';
		const VAT_YES = 'с НДС';
	
	const STAKES_ADD	= 'Новая статья расхода';
	const H2_NEW_SOURCE = 'Новый источник клиентов';
	const H2_COSMETICS  = 'Новая косметика';
	const H2_NEW_SUPPLIER = 'Новый поставщик';
	const HDR_INVOICE_STATE = 'Статус заказа';
	const HDR_VISIT_STATE = 'Статус визита';
		const HDR_VISIT_STATE0  = 'запланирован';
		const HDR_VISIT_STATE1  = 'перенесен';
		const HDR_VISIT_STATE2  = 'ожидает подтверждения';
		const HDR_VISIT_STATE8  = 'отменен';
		const HDR_VISIT_STATE9  = 'no show';
		const HDR_VISIT_STATE10 = 'ок';
	const H2_OVERDUE_VISITS = 'Просроченные визиты';
	const H2_HISTORY = 'История';
	const H2_HISTORY_WORK = 'Расход';
	const HDR_FIRST_SHIPMENT = 'Первая поставка';
	const HDR_LAST_SHIPMENT = 'Последняя поставка';
	const HDR_TOTAL_QTY = 'Закуплено, ед.';
	
	
	
	// Дни недели
	const HDR_WEEKDAY = 'День недели';
	const MONDAY = 'пн.';
	const TUESDAY = 'вт.';
	const WEDNESDAY = 'ср.';
	const THURSDAY = 'чт.';
	const FRIDAY = 'пт.';
	const SATURDAY = 'сб.';
	const SUNDAY = 'вс.';
	
	// Меню
	const MENU_NEW = 'Новый';
	const MENU_REPORTS = 'Отчеты';
	const MENU_PRICELIST = 'Прейскурант';
	const MENU_ANALYTICS = 'Аналитика';
	const MENU_SETTINGS = 'Настройки';
	const MENU_WORKTYPE_CAT = 'Виды услуг';
	const MENU_WORKS = 'Услуги';
	const MENU_LOCATIONS = 'Локации';
	const MENU_LOCATION_DAYS_OFF = 'Выходные дни салона';
	const H2_LOCATIONS = 'Управление локациями';
	const HDR_NEW_LOCATION = 'Новая локация';
	const MENU_USERS = 'Сотрудники';
	const MENU_EXPENCES = 'Расходы';
	const MENU_EXPENCES_CAT = 'Статьи расходов';
	const H2_NEW_CAT = 'Новая статья расходов';
	const MENU_STAKES = 'Ставки постоянных расходов';
	const MENU_EXPENCES_ADD = 'Добавить расход';
	const MENU_EXPENCES_MONTHLY = 'Сопутствующие расходы';
	const TXT_AT = ' в';
	const MENU_CLIENT_SOURCES = 'Источники клиентов';
	const MENU_CLIENTS = 'Клиенты';
	const MENU_VISITS = 'Визиты';
	const MENU_CALENDAR = 'Календарь';
	const SIDE_CALENDAR = 'календарь';
	const MENU_CLIENT_VALUE = 'Ценность клиентов';
	
	
	const TITLE_CLIENT_VISIT = 'Запись на обслуживание';
	const HDR_YOUR_NAME = 'Ваше имя';
	const HDR_CLIENT_SERVICES = 'Какие услуги интересуют?';
	
	
	const MENU_COSMETICS = 'Косметика';
	const H2_BRANDS = 'Бренды';
	const HDR_BRANDS = 'Бренды';
	const H2_NEW_BRAND = 'Новый бренд';
	const H2_SUPPLIERS = 'Поставщики';
	const MENU_PURCHASES = 'Закупки';
	const H2_PURCHASES = 'Закупки в';
	const H2_NEW_INVOICE = 'Новая закупка';
	const HDR_INVOICE = '№ счета';
	const HDR_INVOICE_FROM = ' от ';
	const HDR_INVOICE_DATE = 'Дата счета';
	const HDR_SUPPLIER = 'Поставщик';
	const HDR_QTY = 'Количество позиций';
	const PLACEHOLDER_QTY = 'К-во';
	const PLACEHOLDER_RRP = 'RRP';
	const HDR_DISCOUNT = 'Скидка';
	const PLACEHOLDER_DISCOUNT = 'Скидка (итого)';
	const H2_DAYS_OFF = 'Выходные дни в ';
	const HDR_DAY_OFF = 'Выходной день';
	const HDR_ODD_OR_EVEN = 'Через день';
		const HDR_ODD = 'нечётные дни';
		const HDR_EVEN = 'чётные дни';
	const EXP_USE_FILTER = 'Воспользуйтесь фильтром';
	const HDR_NEW_VISIT = 'Новый визит';
	const HDR_NEW_SALE = 'Новая продажа';
	const H2_FINANCE_REPORT = 'Финансовый результат';
	const H2_SALES_REPORT = 'Отчет по продажам';
	const HDR_REVENUE_TOTAL = 'Доходы, всего';
		const HDR_REVENUE_SERVICES = 'работы';
		const HDR_REVENUE_SALES = 'продажи';
	const HDR_EXPENCES_TOTAL = 'Расходы, всего';
		const HDR_EXPENCES_FIXED = 'постоянные';
		const HDR_EXPENCES_OPERATIONAL = 'операционные';
		const HDR_EXPENCES_COSMETICS = 'закупка косметики';
		const HDR_EXPENCES_WAGES = 'зарплаты';
	const HDR_PROFIT_TOTAL = 'Прибыль, всего';
		const HDR_PROFIT_SALES = 'от продаж';
		const HDR_SALE_PERIOD = 'Дней в продаже';
	const HDR_DATE_SALES_RANGE = 'Дата (закупка / продажа)';
	const HDR_PURCHASE_DETAILS = 'Посмотреть закупку';
	
		
	const TOOLTIP_PROFILE = 'Посмотреть профиль';
	const TOOLTIP_VISIT_START = 'начало в ';
	const TOOLTIP_VISIT_END = 'до ';
	
	//Загрузка файлов
	const HDR_PHOTO = 'Фото';
	const ERR_NOT_AN_IMG = 'Файл не является изображением';
	const ERR_FILE_ALREADY_EXISTS = 'Такой файл уже существует';
	const ERR_FILESIZE = 'Изображение слишком большое. Лимит 500 кб';
	const ERR_FILETYPE = 'Допустимы изображения только в форматах JPG, JPEG, PNG & GIF ';
	
	
	//Профиль клиента
	const H2_INFO_AND_STATS = 'Информация и статистика';
	const HDR_FIRST_VISIT = 'Первое посещение';
	const HDR_LAST_VISIT = 'Последнее посещение';
	const HDR_FREQUENCY = 'Периодичность';
	const HDR_TOTAL_VISITS = 'Всего визитов';
	const HDR_CLIENT_WORTH = 'Стоимость клиента';
		const HDR_WORTH_CURRENT_YEAR = 'текущий год';
		const HDR_WORTH_TOTAL = 'всего';
	const BTN_UPLOAD_PHOTO = 'Загрузить фото';
	const BTN_UPDATE_PHOTO = 'Обновить фото';
	const HDR_CLIENT_PROFILE = 'Посмотреть профиль';
	
}

 ?>