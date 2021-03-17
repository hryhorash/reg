<?php
class lang {
	const DATE = 'Дата';
	
	// Страница логина и уведомления
	const TITLE_LOGIN = 'Авторизація';
	const HDR_LOGIN = 'Ідентифікуйте себе';
	const USERNAME = 'Логін';
	const PASS = 'Пароль';
	const H2_RESTORE_PASS = 'Відновлення паролю';
	const HDR_RESTORE_EMAIL = 'Вкажіть e-mail';
	const RESTORE_PASS = 'Відновити пароль';
	const LANGUAGE = 'Мова інтерфейсу';
	const ERR_CAPTCHA = 'CAPTCHA не було пройдено!';
	const ERR_BLOCKED = 'Акаунт заблоковано. Будь-ласка, зверніться до адміністратора';
	const ERR_PASS = 'Пароль введено невірно';
	const ERR_PASS_DONT_MATCH = 'Паролі не співпадають';
	const ERR_NO_SUCH_USER = 'Такого користувача не існує';
	const ERR_NO_SUCH_EMAIL = 'Користувача з таким e-mail не існує';
	const ERR_NO_RIGHTS = 'Недостатньо повноважень';
	const ERR_NO_WAY = 'Видалення неможливо';
	const ERR_NO_ID = 'Не зазначено ідентифікатор';
	const ERR_NO_CLIENT = 'Не зазначено клієнта';
	const ERR_NO_STAFF = 'Зазначте відповідальну особу';
	const ERR_NO_INFO = 'Дані для відображення відсутні';
	const ERR_SELECT_LOCATION = 'Необхідно обрати хоча би один салон зі списку';
	const ERR_SELECT_SPECIALTY = 'Зазначте хоча б один вид робіт';
	const ERR_NO_PRICE = 'Необхідно зазначити вартість';
	const ERR_NO_RECOMMENDATION = 'Клієнта за рекомендацією зазначено невірно';
	const ERR_NO_GENDER = 'Необхідно зазначити стать клієнта';
	const ERR_CONSTRAINT = 'Видалення неможливо';
	const ERR_SPECIFY_DAY = 'Необхідно вказати день';
	const ERR_PHONE = 'Напишіть телефон в форматі 380ххххххххх будь-ласка';
	const ERR_GENERAL = 'Сталася помилка';
	const SUCCESS_PASS = 'Пароль змінено';
	const SUCCESS_DELETE = 'Запис видалено';
	const SUCCESS_RESTORE = 'Доступ відновлено';
	const SUCCESS_GENERAL = 'Зміни збережено';
	const SUCCESS_GENERAL_ADD = 'Запис додано';
	const SUCCESS_USER_BLOCKED = 'Користувача заблоковано';
	const SUCCESS_NEW_VISIT = 'Дякую! Вашу заявку прийнято. <br />Очікуйте на підтвердження';
	const BTN_ENTER = 'Увійти';
	const BTN_DONE = 'Готово';
	const BTN_CANCEL = 'Скасувати';
	const BTN_CLIENT_VISIT = 'Записатися';
	const MSG_SOLD = '. Продано ';
	const HDR_AVG_DURATION = 'Середня тривалість';
	const HDR_RATE_PLACEHOLDER = 'cтавка';
	const H2_GOODS_TO_SELL = 'Косметика на продаж у';
	const H2_WORK_COSM_AVAILABLE = 'Баланс витратної косметики в';
	const HDR_AVAILABILITY = 'Наявність';
		const HDR_PCS = 'од.';
		const HDR_GR = 'гр.';
	const HDR_COST_PER_GR = 'Ціна за гр.';
	
	
	// Имена ролей
	const LEVEL_BASIC = 'Майстер';
	const LEVEL_ADMIN = 'Адміністратор';
	const LEVEL_GENERAL = 'Керівник';
	const LEVEL_GODMODE = 'Повний доступ';
	const GODMODE_LOCATIONS = 'всі';
	
	//TABS
	const TAB_ACTIVE = 'Актуальні';
	const TAB_ACTIVE_M = 'Актуальний';
	const TAB_ACTIVE_F = 'Активна';
	const TAB_ARCHIVE = 'архів';
	const TAB_BRANDS = 'Бренди';
	const TAB_COSMETICS = 'косметика';
	const TAB_SUPPLIERS = 'Постачальники';
	const TAB_ALL = 'всі';
	const TAB_DAYS_OFF = 'Выхідні';
	const TAB_WORKDAYS = 'Графік роботи';
	const TAB_LOCATIONS = 'Локації';
	const TAB_USERS = 'Співробітники';
	const TAB_LIST	= 'cписок';
	const TAB_IN_PROGRESS	= 'в роботі';
	const TAB_ADD	= 'додати';
	const TAB_EXTRA	= 'супутні витрати';
	const TAB_WORKDAYS_USERS = 'Графік роботи співробітників';
	const TAB_PLANS = 'плани';
	const TAB_SELL = 'продати!';
	
	//SIDEBAR
	const SIDEBAR_FILTERS = 'Фільтри:';
	
	// ФОРМЫ
	const SELECT_DEFAULT = 'Оберіть';
	const SELECT_ALL = 'Показати все';
	const SELECT_ADD_ALL = ' (всі)';
	const OLD_PASS = 'Старий пароль';
	const NEW_PASS = 'Новий пароль';
	const CONFIRM_PASS = 'Підтвердіть пароль';
	const HDR_NAME = 'Ім`я';
	const HDR_EMAIL = 'E-mail';
	const HDR_CITY = 'Місто';
	const HDR_LOCATION = 'Салон';
	const HDR_LOCATION_PLURAL = 'Салони';
	const HDR_OPERATING_HOURS = 'Години роботи';
	const HDR_OPEN_FROM = ' від';
	const HDR_OPEN_TILL = ' до';
	const HDR_WORKTYPE_CAT = 'Вид послуг';
	const HDR_WORKTYPE_CATS = 'Види послуг';
	const H2_WORKTYPE_CAT = 'Новий вид послуг';
	const H2_WORKTYPE_NEW = 'Нова послуга';
	const HDR_WORKTYPE = 'Послуга';
	const HDR_ITEM_NAME = 'Найменування';
	const HDR_ENTITY_NAME = 'Назва';
	const CHANGE_ITEM_NAME = 'Змінити назву';
	const HDR_WORKTYPE_TARGET = 'Для кого';
	const HDR_PRICE = 'Ціна';
	const HDR_WORKTYPE_PRICE_RANGE = 'Діапазон цін';
	const HDR_WORKTYPE_MINPRICE = 'Ціна від';
	const HDR_WORKTYPE_MAXPRICE = 'Ціна до';
	const HDR_ACTIVE_FROM = 'Діє з';
	const HDR_COST = 'Вартість';
	const PER_PIECE_PLACEHOLDER = 'на день';
	const PER_MONTH_PLACEHOLDER = 'або на місяць';
	const PHONE_PLACEHOLDER = 'телефон';
	const PER_MONTH = ' на місяць'; //пробел в начале нужен!
	const HDR_MONTH = 'Місяць'; 
	const HDR_TOTAL = 'Всього'; 
	const HDR_BRAND = 'Бренд '; 
	const HDR_RRP = 'Рекомендована ціна'; 
	const HDR_DESCRIPTION = 'Опис'; 
	const HDR_CONTACTS = 'Контакти'; 
	const HDR_NEW_DAY_OFF = 'Новий вихідний'; 
	const HDR_WORKDAY = 'Робочі дні'; 
	const HDR_NEW_WORKDAY = 'Новий робочий день'; 
	const HDR_TIME = 'Час (з/по)'; 
	const HDR_TIME_FROM = 'Час початку'; 
	const HDR_TIME_TO = 'Час закінчення'; 
	const HDR_VISIT_DURATION = 'Тривалість'; 
	const HDR_TOTAL_PRICE_RANGE = 'Підсумковий діапазон цін'; 
	const HDR_TO_PAY = 'До сплати'; 
	const HDR_VISIT_DATA = 'Дані візиту'; 
	const HDR_WORKTYPE_LIST = 'Замовлені послуги'; 
	const HDR_NETTO_SEVICES = 'Супутні послуги'; 
	const HDR_SPENT_LIST = 'Витратна косметика'; 
	const HDR_SALES_LIST = 'Продажі'; 
	const HDR_SPENT_VOLUME = 'гр.'; 
	const HDR_COST_PER_EMPLOYEE = 'Робітники та вартість'; 
	const HDR_TIPS = 'Чай'; 
	const HDR_VISIT_TOTALS = 'Підсумки візиту'; 
	const HDR_CUSTOMER_SERVICE = 'Обслуговування клієнта'; 
	const HDR_NETTO = 'Нетто'; 
	const HDR_PROFIT = 'Прибуток'; 
	const HDR_WAGE = 'Зарплата'; 
	const HDR_SERVICE_NETTO = 'Собівартість послуг'; 
	const HDR_ADD_SERVICE_NETTO = 'Додати собівартість послуги'; 
	const HDR_SERVICES_NETTO = 'Витрати нетто на'; 
	const MENU_WAREHOUSE = 'Склад'; 
	const HDR_SERVICES = 'Послуги'; 
	const HDR_FORMULA = 'Формула'; 
	const HDR_SALE_ARCHIVE = 'Архів продажів'; 
	const HDR_ARCHIVE = "Архів";
	
	const HDR_CATEGORY = 'Категорія';
	const HDR_SUBCATEGORY = 'Підкатегорія';
	const HDR_SHOW_IN_MENU = 'Відображення в меню';
	
	const HDR_ROLE = 'Рівень доступу';
	const HDR_CHANGE_PASS = 'Змінити пароль';
	const BTN_SHOW = 'Показати';
	const BTN_ADD = 'Додати';
	const BTN_ADD_WORK = 'Додати послугу';
	const BTN_ADD_SPENT = 'Додати витрати';
	const BTN_ADD_EMPLOYEE = 'Додати робітника';
	const BTN_ADD_SALE = 'Додати продаж';
	const BTN_SELL = 'Продати';
	const BTN_CHANGE = 'Змінити';
	const BTN_NEXT = 'Далі';
	const BTN_SAVE = 'Зберегти зміни';
	
	//Кнопки управления
	const HDR_HANDLING = 'Керування';
	const HANDLING_VIEW = 'Детальніше';
	const HANDLING_CHANGE = 'Редагувати';
	const HANDLING_UNDO = 'Скасувати продаж';
	const HANDLING_BLOCK = 'Заблокувати';
	const HANDLING_ARCHIVE = 'Перенести до архіву';
	const HANDLING_APPROVE = 'Візит відбувся';
	const HANDLING_NOSHOW = 'Візит не відбувся. No show';
	const HANDLING_DELETE = 'Видалити';
	const ALERT_BLOCK_DEFAULT = 'Перенести до архіву ';	//пробел в конце нужен
	const ALERT_BLOCK = 'Заблокувати користувача ';	//пробел в конце нужен
	const ALERT_BLOCK_LOCATION = 'Відправити до архіву ';
	const ALERT_DELETE = 'Увага! Операція не підлягає скасуванню\nВидалити ';	//пробел в конце нужен
	const ALERT_CONFIRM_VISIT = 'Відмітити як такий, що відбувся без змін?';
	const ALERT_NOSHOW = 'Клієнт не прийшов?';
	const ALERT_RESTORE_LOCATION = 'Активувати локацію ';
	const HANDLING_RESTORE = 'Відновити '; 			//пробел в конце нужен
	const ALERT_WRONG_DATE_STATE = 'Цей статус недоступний для дат у майбутньому';
	const ALERT_EXCEED_LIMIT = 'Для збільшення кількості натисніть "' . lang::BTN_ADD_SALE . '"';
	const ALERT_EXCEED_MAX = 'Такої кількості немає в наявності. Буде встановлено максимально доступне';
	const HDR_CALENDAR_TAKEN = 'зайнято';
	
	
	//Управление доступом к программе
	const HDR_ACCESS_LIST = 'Співробітники';
	const HDR_ACCESS_EDIT = 'Права доступу для ';
	const HDR_NEW_USER = 'Новий користувач';
	const NAME = 'Ім`я';
	const SURNAME = 'Прізвище';
	const HDR_PHONE = 'Телефон';
	const HDR_PHONES = 'Телефони';
	const PHONE_PLACEHOLDER_PATTERN = '380xxxxxxxxx';
	const HDR_COMMENT = 'Коментар';
	const COMMENT_PLACEHOLDER = 'Коментар';
	const HDR_RECOMMENDATION = 'За рекомендацією';
	const HDR_GENDER = 'Стать';
		const HDR_MALE = 'чоловіча';
		const HDR_FEMALE = 'жіноча';
	
	const H2_NEW_CLIENT = 'Новий клієнт';
	const TBL_CLIENT = 'Клієнт';
	const HDR_PROMPT = 'Підказка';
	const HDR_CLIENT_SOURCE = 'Джерело';
	const HDR_DOB = 'День народження';
	const HDR_VOLUME = 'Місткість';
	const HDR_PURPOSE = 'Призначення';
		const HDR_PURPOSE_WORK = 'в роботі';
		const HDR_PURPOSE_SALE = 'на продаж';
		const HDR_PURPOSE_BOTH = 'універсальна';
		const HDR_PURPOSE_ACCOUNT = 'облік';
	const HDR_ARTICUL = 'Артикул';
	const HDR_CONTACT_NAME = 'Контактна особа';
	const HDR_CONTACT_POSITION = 'Посада';
	const HDR_SITE = 'Сайт';
	const HDR_ADDRESS = 'Адреса';
	const HDR_PURCHASE_STATE = 'Статус заказу';
		const PURCHASE_STATE0 = 'формується';
		const PURCHASE_STATE1 = 'відправлено постачальнику';
		const PURCHASE_STATE2 = 'рахунок отримано';
		const PURCHASE_STATE3 = 'рахунок сплачено';
		const PURCHASE_STATE4 = 'замовлення отримано';
		const PURCHASE_STATE5 = 'замовлення отримано та оплачено';
	const HDR_EXPIRE = 'Строк придатності';
	const HDR_DATE_PAID = 'Дата оплати';
	const HDR_DATE_RECEIVED = 'Дата отримання';
	const NO_NAME = 'Без назви';
	const HDR_EMPLOYEE = 'Співробітник';
	
	const EXPL_NETTO_PRICE = 'Суми вказуються довідково. Дані не враховуються у фін. звітах';
	const EXPL_WORK_PRICE = 'Сума вказується тільки якщо необхідно вирахувати % зарплатні робітника';
	const EXPL_PRICE_PER_GR = 'Орієнтовно. За основу береться найбільш дорога позиція із закупівель';
	
	const TXT_BALANCE = 'Баланс за період';
	
	//HEAD
	const SEARCH_CLIENT_PLACEHOLDER = 'ПІБ клієнта';
	const LOGOUT = 'Вийти';
	
	
	//e-mail
	const EMAIL_SUBJECT = 'Відновлення паролю';
	const EMAIL_YOUR_DATA = 'Ваші дані для входу:';
	const EMAIL_OPTIONS = 'Ви можете змінити пароль у своєму профілі';
	const EMAIL_WARNING = 'Якщо ви не відправляли запит на відновлення паролю, проігноруйте цього листа.';
	const EMAIL_SENT = 'Новий пароль відправлено на e-mail ';
	
	
	// Опции для видов услуг
	const TARGET_MALE = 'для чоловіків';
	const TARGET_FEMALE = 'для жінок';
	const TARGET_CHILD = 'для дітей';
	const TARGET_GENERAL = 'універсальна';
	
	const HDR_VAT = 'Ціны';
		const VAT_NO = 'без ПДВ';
		const VAT_YES = 'з ПДВ';
	
	const STAKES_ADD	= 'Нова стаття витрат';
	const H2_NEW_SOURCE = 'Нове джерело клієнтів';
	const H2_COSMETICS  = 'Нова косметика';
	const H2_NEW_SUPPLIER = 'Новий постачальник';
	const HDR_INVOICE_STATE = 'Статус замовлення';
	const HDR_VISIT_STATE = 'Статус візиту';
		const HDR_VISIT_STATE0  = 'заплановано';
		const HDR_VISIT_STATE1  = 'перенесено';
		const HDR_VISIT_STATE2  = 'очікує на підтвердження';
		const HDR_VISIT_STATE8  = 'скасовано';
		const HDR_VISIT_STATE9  = 'no show';
		const HDR_VISIT_STATE10 = 'ок';
	const H2_OVERDUE_VISITS = 'Прострочені візити';
	const H2_HISTORY = 'Історія';
	const H2_HISTORY_WORK = 'Витрати';
	const HDR_FIRST_SHIPMENT = 'Перша поставка';
	const HDR_LAST_SHIPMENT = 'Остання поставка';
	const HDR_TOTAL_QTY = 'Закуплено, од.';
	
	
	
	// Дни недели
	const HDR_WEEKDAY = 'День тижня';
	const MONDAY = 'пн.';
	const TUESDAY = 'вт.';
	const WEDNESDAY = 'ср.';
	const THURSDAY = 'чт.';
	const FRIDAY = 'пт.';
	const SATURDAY = 'сб.';
	const SUNDAY = 'вс.';
	
	// Меню
	const MENU_NEW = 'Новий';
	const MENU_REPORTS = 'Звіти';
	const MENU_PRICELIST = 'Прейскурант';
	const MENU_ANALYTICS = 'Аналітика';
	const MENU_SETTINGS = 'Налаштування';
	const MENU_WORKTYPE_CAT = 'Види послуг';
	const MENU_WORKS = 'Послуги';
	const MENU_LOCATIONS = 'Локації';
	const MENU_LOCATION_DAYS_OFF = 'Неробочі дні закладу';
	const H2_LOCATIONS = 'Управління локаціями';
	const HDR_NEW_LOCATION = 'Нова локація';
	const MENU_USERS = 'Співробітники';
	const MENU_EXPENCES = 'Витрати';
	const MENU_EXPENCES_CAT = 'Статті витрат';
	const H2_NEW_CAT = 'Нова стаття витрат';
	const MENU_STAKES = 'Ставки постійних витрат';
	const MENU_EXPENCES_ADD = 'Додати витрату';
	const MENU_EXPENCES_MONTHLY = 'Супутні витрати';
	const TXT_AT = ' у';
	const MENU_CLIENT_SOURCES = 'Джерела клієнтів';
	const MENU_CLIENTS = 'Клієнти';
	const MENU_VISITS = 'Візити';
	const MENU_CALENDAR = 'Календар';
	const SIDE_CALENDAR = 'календар';
	const MENU_CLIENT_VALUE = 'Цінність клієнтів';
	const MENU_WAGES = 'Зарплати';
	
	
	const TITLE_CLIENT_VISIT = 'Запис на обслуговування';
	const HDR_YOUR_NAME = 'Ваше ім`я';
	const HDR_CLIENT_SERVICES = 'Які послуги цікавлять?';
	
	
	const MENU_COSMETICS = 'Косметика';
	const H2_BRANDS = 'Бренди';
	const HDR_BRANDS = 'Бренди';
	const H2_NEW_BRAND = 'Новий бренд';
	const H2_SUPPLIERS = 'Постачальники';
	const MENU_PURCHASES = 'Закупівлі';
	const H2_PURCHASES = 'Закупівлі в';
	const H2_NEW_INVOICE = 'Нова закупівля';
	const HDR_INVOICE = '№ рахунку';
	const HDR_INVOICE_FROM = ' від ';
	const HDR_INVOICE_DATE = 'Дата рахунку';
	const HDR_SUPPLIER = 'Постачальник';
	const HDR_QTY = 'Кількість позицій';
	const PLACEHOLDER_QTY = 'К-ть';
	const PLACEHOLDER_RRP = 'RRP';
	const HDR_DISCOUNT = 'Знижка';
	const PLACEHOLDER_DISCOUNT = 'Знижка (всього)';
	const H2_DAYS_OFF = 'Виходні дні в ';
	const HDR_DAY_OFF = 'Виходний день';
	const HDR_ODD_OR_EVEN = 'Через день';
		const HDR_ODD = 'непарні дні';
		const HDR_EVEN = 'парні дні';
	const EXP_USE_FILTER = 'Скористайтеся фильтром';
	const HDR_NEW_VISIT = 'Новий візит';
	const HDR_NEW_SALE = 'Новий продаж';
	const H2_FINANCE_REPORT = 'Фінансовий результат';
	const H2_SALES_REPORT = 'Звіт з продажів';
	const H2_EXPENCES_WORKS_MATCH = 'Витрати та роботи';
	const HDR_REVENUE = 'Дохід';
	const HDR_REVENUE_TOTAL = 'Доходи, всього';
		const HDR_REVENUE_SERVICES = 'роботи';
		const HDR_REVENUE_SALES = 'продажі';
	const HDR_EXPENCES_TOTAL = 'Витрати, всього';
		const HDR_EXPENCES_FIXED = 'постійні';
		const HDR_EXPENCES_OPERATIONAL = 'операційні';
		const HDR_EXPENCES_COSMETICS = 'закупівлі косметики';
		const HDR_EXPENCES_WAGES = 'зарплати';
	const HDR_PROFIT_TOTAL = 'Прибуток, всього';
		const HDR_PROFIT_SALES = 'від продажів';
		const HDR_SALE_PERIOD = 'Днів у продажу';
	const HDR_DATE_SALES_RANGE = 'Дата (закупівля / продаж)';
	const HDR_PURCHASE_DETAILS = 'Переглянути закупівлю';
	const H2_FINANCE_CAT = 'Звіт за категоріями робіт';
		
	const TOOLTIP_PROFILE = 'Переглянути профіль';
	const TOOLTIP_VISIT_START = 'початок об ';
	const TOOLTIP_VISIT_END = 'до ';
	
	//Загрузка файлов
	const HDR_PHOTO = 'Фото';
	const ERR_NOT_AN_IMG = 'Файл не є зображенням';
	const ERR_FILE_ALREADY_EXISTS = 'Такий файл уже існує';
	const ERR_FILESIZE = 'Зображення завелике. Ліміт 500 кб';
	const ERR_FILETYPE = 'Зображення прийнятні лише у форматах JPG, JPEG, PNG & GIF ';
	
	
	//Профиль клиента
	const H2_INFO_AND_STATS = 'Інформація та статистика';
	const HDR_FIRST_VISIT = 'Перший візит';
	const HDR_LAST_VISIT = 'Останній візит';
	const HDR_FREQUENCY = 'Періодичність';
	const HDR_TOTAL_VISITS = 'Всього візитів';
	const HDR_CLIENT_WORTH = 'Вартість клієнта';
		const HDR_WORTH_CURRENT_YEAR = 'поточний рік';
		const HDR_WORTH_TOTAL = 'всього';
	const BTN_UPLOAD_PHOTO = 'Завантажити фото';
	const BTN_UPDATE_PHOTO = 'Оновити фото';
	const HDR_CLIENT_PROFILE = 'Переглянути профіль';
	
	// Отчеты
	const H2_VISITS_REVENUE_PER_DAY = 'Візити по днях';
	const H2_FINANCE_YEARLY = 'Финансовий результат (рік)';
	const HDR_ANALYSIS = 'Аналіз';
	const HDR_PROFIT_PER_MONTH_AVG = 'Прибуток на місяць, середній';
	const HDR_EXPENCES_TO_REVENUE = 'Доля витрат в обороті';
	const H2_CLIENT_TREE = 'Дерево клієнтів';
		const HDR_LEVEL2 = '2 рівень';
		const HDR_LEVEL3 = '3 рівень';
		const HDR_MORE_LVL = 'ще (рівні 4-6)';
}

 ?>