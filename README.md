<p>Сервис дерева комментариев к двум постам с аторизацией и CRUD</p>
<p>Сервис работает через AJAX, получает на view JSON каждый раз после эвента работы с комментариями </p>
<p>Авторизация простейшая, хардкод их пакета</p>

<p>Проект выложен на домен http://kingmastodon.com/web/ , работает.</p>
</br>
<p>Роутам не уделял внимания, разрабатывал под локалхост без yii serve, соответственно в путях фигурирует каталог web.</p>
<p>JQuerry не вынес в отдельный файл, было очень удобно разрабатывать фронт во view.php. В идеале можно вынести</p>
<p>Порядок разворачивания:</p>
<ul>
<li> Если тестирование на локалхост то файлы проекта сложить в каталог gjbeta.</li>
<li> Запускать без yii serve, композер апдейтить.</li>
<li> Если тестирование на шаред хостинге, файлы проекта сложить в корень сайта, в файле views\posts\view.php JS-константу под названием ‘root’ сделать пустой стрингой.</li>
<li> PHP не ниже 7.4.28 (на локале 8.1 но на хостинге выше 7.4 не взлетает по какой-то причине)</li>
</ul>
 

<p>Моего кода много в models, views, controllers особенно views/post/view.php, контроллеры, модели и серчмодели: с именами включающими comment и post. </p>