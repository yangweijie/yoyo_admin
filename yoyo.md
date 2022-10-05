# 工作原理

Yoyo 组件 是在页面加载时渲染的基于用户交互和指定事件可以独立更新，无需重载页面的元素。

组件更新请求被发送到一个Yoyo设计好的路由里，当它处理好请求然后发送回更新过的组件 html 片段给浏览器。

你可以从服务器直接更新浏览器 url 状态 和 触发浏览器事件。

下面你可以看到一个计数组件大概是什么样子：

**Component class**

```php
# /app/Yoyo/Counter.php

<?php 
namespace App\Yoyo;

use Clickfwd\Yoyo\Component;

class Counter extends Component
{
	public $count = 0;
	
	protected $props = ['count'];

    public function increment()
    {
        $this->count++;
    }
}
```

**Component template**

```html
<!-- /app/resources/views/yoyo/counter.php -->

<div>

	<button yoyo:get="increment">+</button>
	
	<span><?php echo $count; ?></span>

</div>
```

是的，就是那么的简单！上面有一个值得注意的点，使用了 protected 属性 `$props`。这向 Yoyo 表明，模板中未显式使用的 `count` 变量 应在每个请求中保留和更新。


## 安装

### 安装包

```bash
composer require clickfwd/yoyo

cp vendor/clickfwd/src/assets/js/yoyo.js public/static/js/yoyo.js
```

## 更新

在运行 `composer update`之后, 记得也更新 `yoyo.js`脚本。

## 配置 Yoyo

在经过一些配置之后才能启动 Yoyo。这些配置应该在渲染和更新组件之前运行。

```php
use Clickfwd\Yoyo\View;
use Clickfwd\Yoyo\ViewProviders\YoyoViewProvider;
use Clickfwd\Yoyo\Yoyo;

$yoyo = new Yoyo();

$yoyo->configure([
  'url' => '/yoyo',
  'scriptsPath' => 'app/resources/assets/js/',
  'namespace' => 'App\\Yoyo\\'
]);

// Register the native Yoyo view provider 
// Pass the Yoyo components' template directory path in the constructor

$yoyo->registerViewProvider(function() {
  return new YoyoViewProvider(new View(__DIR__.'/resources/views/yoyo'));
});
```

**'url'**
组件更新会用到的相对或绝对 URL。

**'scriptsPath'**

你复制`yoyo.js` 到资源目录里的路径。可以是相对的。

**'namespace'**

用来发现 自动动态加载组件（使用 PHP类定义的）的命名空间。

如果命名空间未提供，或者组件在不同命名空间里，你需要手动注册它们:

```php
$yoyo->registerComponents([
    'counter' => App\Yoyo\Counter::class,
];
```
你被要求在运行时加载 组件的类，既可以通过使用 `require` 短语来加载组件类文件，也可以通过在你的项目 `composer.json` 文件里包含所需的命名空间。

匿名组件不需要被注册，但是模板名称需要匹配组件名称。

### 加载资源

找到 `yoyo.js` 在以下 vendor 路径里，然后复制到你项目的public 资源目录里。

```file
/vendor/clickfwd/yoyo/src/assets/js/yoyo.js 
```
在你的模板 `<head>` 标签后 添加以下代码来加载必要的脚本。

```php
<?php yoyo_scripts(); ?>
```

## 创建组件

动态组件需要一个类和一个模板。你可以使用行内视图，当组件标记 直接返回 组件的 render 方法。

匿名组件允许创建组件只有一个模板文件。

要创建一个从服务端检索结果更新自身的组件，创建以下组件模板:

```html
// resources/views/yoyo/search.php

<form>
    <input type="text" name="query" value="<?php echo $query ?? ''; ?>">
    <button type="submit">Submit</button>
</form>
```

Yoyo 将渲染组件输出并编译它来添加必要的属性使之动态检索。
当你提交表单时，提交的数据自动的在模板里可使用。
模板代码扩展了来显示一个结果的列表或者空状态：

```php
<?php
$query = $query ?? '';
$entries = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$results = array_filter($entries, function($entry) use ($query) {
    return $query && strpos($entry, $query) !== false;
});
?>
```

```html
<form>
    <input type="text" name="query" value="<?php echo $query; ?>">
    <button type="submit">Submit</button>
</form>
    
<ul>
    <?php if ($query && empty($results)): ?>
        <li>No results found</li>
    <?php endif; ?>
    
    <?php foreach ($results as $entry): ?>
        <li><?php echo $entry; ?></li>
    <?php endforeach; ?>
</ul>
```

`$results` 数组可以替换为任意来源 (i.e. database, API 等)。

示列可以转换为一个实时搜索框，有这一个300ms 延迟来最小化检索请求。
替换 `form` 标签为下面的:

```html
<input yoyo:on="keyup delay:300ms changed" type="text" name="query" value="<?php echo $query; ?>" />
```

`yoyo:on="keyup delay:300ms change"` 指令告诉 Yoyo 来发起一次请求在 keyup 事件触发时,并且有一个 300ms 延迟，并且只有当输入框文本变化的时候。 

现在 让我们把这些转换为一个动态组件通过一个类。

```php
# /app/Yoyo/Search

<?php

namespace App\Yoyo;

use Clickfwd\Yoyo\Component;

class Search extends Component
{
	public $query;
	
	protected $queryString = ['query'];
	
	public function render()
	{
		$query = $this->query;
	
		// Perform your database query
		$entries = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
	
		$results = array_filter($entries, function($entry) use ($query) {
			return $query && stripos($entry, $query) !== false;
		});
	
	  // Render the component view
		return $this->view('search',['results' => $results]);
	}
}
```

接下来是模板:

```html
<!-- /app/resources/views/yoyo/search.php -->

<input yoyo:on="keyup delay:300ms changed" type="text" name="query" value="<?php echo $query; ?>" />

<ul yoyo:ignore>
    <?php if ($query && empty($results)): ?>
        <li>No results found</li>
    <?php endif; ?>
    
    <?php foreach ($results as $entry): ?>
        <li><?php echo $entry; ?></li>
    <?php endforeach; ?>
</ul>
```

这里有一些值得注意的点，在其他章节里也会涉及。

1. 组件类包含了一个 `queryString` 属性告诉 Yoyo 来自动的包含 浏览器URL里的 queryString 的值，在一个组件更新后。如果你重新加载页面url带着 `query` 值，你将自动在页面上看到检索的结果。

2. Yoyo 将自动组件类里公共属性作为模板里的变量。这允许使用 `$this->query` 来访问组件里搜索的关键字，并且模板中 `$query` 可用。

当你开始比较搜索示列 和 计数示列，你会发现 那里没有 动作方法（也就是 increment, decrement）。一个组件更新总是默认为 `render` 方法, 除非 通过方法属性指定了 (i.e. yoyo:get, yoyo:post 等等)。那种情况下，动作方法将永远在 render 之前运行。

## 渲染组件

有两种情况下组件会被渲染。当页面加载，还有组件更新时。

### 页面加载时渲染

要在你的模板页面加载时渲染任何组件，使用 `yoyo_render` 函数 并且传递组件名称作为第一个参数。

```php
<?php echo yoyo_render('search'); ?>
```

作为动态组件，组件名通常是一个类名的连字符形式（i.e. LiveSearch → live-search）。如果在启动Yoyo时你用了 `registerComponents` 方法，那么你可以使用注册的别名。

```php
$yoyo->registerComponent('search', App\Yoyo\LiveSearch::class);
```

对于匿名组件，组件名应当和无后缀的模板名匹配。如果你的模板名是`form.php`，组件可以通过以下代码渲染 ：

```php
<?php echo yoyo_render('search'); ?>
```

### 渲染和更新

使用 `yoyo_update` 函数来自动化处理传递给组件的请求并输出更新后的组件。

```php
<?php echo yoyo_update(); ?>
```

你需要 添加这个为所有请求 处理 的函数的调用 路由到你的初始化Yoyo 配置里的 `url`。

## 属性

在动态组件里，所有组件类里的公共属性将自动作为视图可用变量并且在组件更新里追踪。

```php
class HelloWorld extends Component
{
    public $message = 'Hello World!';
}
```

```html
<div>
    <h1><?php echo $message; ?></h1>
    <!-- Will output "Hello World!" -->
</div>
```

公共属性应当只能为以下类型：`string`, `int`, `array`, `boolean`, 并且不能包含任何敏感信息，因为他们会被同步数据的请求所使用。

### 初始化属性

你可以使用组件里的 `mount` 方法初始化属性，其将在组件实例化时立即运行，在 `render` 方法之前。

```php
class HelloWorld extends Component
{
    public $message;

    public function mount()
    {
        $this->message = 'Hello World!';
    }
}
```

### 数据绑定
你可以自动绑定，或者同步 一个 HTML 元素的值通过组件的一个公共属性。

```php
class HelloWorld extends Component
{
    public $message = 'Hello World!';
}
```

```html
<div>
    <input yoyo name="message" type="text" value="<?php echo $message; ?>">
    <h1><?php echo $message;?></h1>
</div>
``` 
添加 `yoyo` 到任何输入框将立刻使其具有响应性。任何输出框的变化将在组件中更新。

默认的，元素的自然事件将用作事件触发器。

- input, textarea and select 元素在change 事件时触发。
- form 元素 在提交事件时触发。
- 其他元素在点击事件时触发。

你可以通过 `yoyo:on` 指令接受多个逗号分割的事件来修改这个行为：

 ```html
 <input yoyo:on="keyup" name="message" type="text" value="<?php echo $message; ?>">
 ```

### 防抖动和限制请求

有几种方式来限制更新组件的请求。

**`delay`** - 防抖动请求 这样使在指定的间隔后请求在最后的事件触发时。

```html
<input yoyo:on="keyup delay:300ms" name="message" type="text" value="<?php echo $message; ?>">
```

**`throttle`** 限制指定间隔里只请求一次。

```html
<input yoyo:on="input throttle:2s" name="message" type="text" value="<?php echo $message; ?>">
```

**`changed`** - 只有当输入框的值变化了才请求。

```html
<input yoyo:on="keyup delay:300ms changed" name="message" type="text" value="<?php echo $message; ?>">
```

## 动作

一个动作是一个请求使得一个 Yoyo组件的方法来更新（重新渲染）自身作为一个用户交互或者页面事件的结果。(click, mouseover, scroll, load, etc.)。

`render` 方法是默认动作 当没有指定动作的时的默认值。你也可以在类重写它来改变模板名称或当你传递除了公共属性之外的变道模板视图时。

```php
public function render() 
{
	return $this->view($this->componentName, ['foo' => 'bar']);
}
```

要指定一个动作当你使用下面可用动作指令且方法名做为指定值。

- `yoyo:get`
- `yoyo:post`
- `yoyo:put`
- `yoyo:patch`
- `yoyo:delete`

举例:

```php
class Review extends Component
{
    public Review $review;

    public function helpful()
    {
        $this->review->userFoundHelpful($userId);
    }
}
```

```html
<div>
    <button yoyo:on="click" yoyo:get="helpful">Found Helpful</button>
</div>
```

所有组件将自动监听 `refresh` 事件，并且触发渲染动作来刷新组件状态。

### 给动作传递数据

你可以包含额外数据发送到服务端在组件更新请求里通过使用 `yoyo:vals` 指令其接收一个directive which accepts a JSON encoded 的键值对对象。

```html
<button yoyo:on="click" yoyo:get="helpful" yoyo:vals='{"reviewId":100}'>Found Helpful</button>

<!-- Or use the encode_vals helper function to pass an array of name-value pairs -->
<button yoyo:on="click" yoyo:get="helpful" yoyo:vals='<?php Yoyo\encode_vals(["reviewId"=> 100]); ?>'>Found Helpful</button>
```

你也可以使用 `yoyo:val.name` 来给单个值赋值。 kebab-case 连字符 变量名称 将自动被转换为驼峰。

```html
<button yoyo:on="click" yoyo:get="helpful" yoyo:val.review-id="100">Found Helpful</button>
```

Yoyo 将自动追踪并且发送组件的公共属性和输入框的值在每次请求。

```php
class Review extends Component {

	public $reviewId;

	public function helpful()
	{
		// access reviewId via $this->reviewId
	}
}
```

你也可以传递额外参数到一个动作 作为参数 用 一个输出表达式，不需要考虑定义他们到组件的公共属性：

```html
<button yoyo:get="addToCart(<?php echo $productId; ?>, '<?php echo $style; ?>')">
    Add Todo
</button>
```

额外参数传递到动作里可以在组件方法里作为常规参数使用：

```php
public function addToCart($productId, $style)
{
    // ...
}
```

### 动作没有一个响应

有时候你可能想要使用一个组件动作只是改变数据库触发事件，而不需要渲染一个响应。你可以使用组件的`skipRender`方法来达成这个目的：

```php
public function savePost() 
{
	// Store the post to the database

	// Send event to the browser to close modal, or trigger a notification
	$this->emitSelf('PostSaved');

	// Skip template rendering
	$this->skipRender();
}
```

## 视图数据

有时候你想要发送数据到一个视图不需要声明一些公共属性的变量。 你可以通过定义个渲染方法在你的组件里，然后传递一个数据数组作为第二参数：

```php
public function render() 
{
	return $this->view($this->componentName, ['foo' => 'bar']);
}
```

然后你可以在模板中访问 $foo 变量。

你也可以发送数据到组件视图里通过 在组件动作里 使用`set` 方法。 举例:

```php
public function increment()
{
	$this->set('foo', 'bar');
	// or
	$this->set(['foo' => 'bar']);
}
```

## 计算属性

```php
class HelloWorld extends Component
{
	public $message = 'Hello World!';
	
   	// Computed Property
	public function getHelloWorldProperty()
	{
		return $message;
	}

   	// Computed Property with argument
	public function getErrorsProperty($name)
	{
		return [
			'title' => 'Please enter a title',
			'description' => 'Please enter a description',
		][$name] ?? null;
	}
}
```
	
现在，你可以从组件的类或模板中访问 `$this->hello_world` :

```php
<div>
	<h1><?php echo $this->hello_world ;?></h1>
	<!-- Will output "Hello World!" -->
</div>
```

你可以在模板中使用带参的计算属性行为类似正常类的方法:

```php
<div>
	<h1><?php echo $this->errors('title') ;?></h1>
	<!-- Will output "Please enter a title" -->
</div>
```

计算属性的输出被同样组件请求缓存了允许你运行复杂的任务比如查询数据库请求多次并不会重复。如果你需要清楚一个计算属性的缓存：

```php
// Clear all computed properties, including those with arguments
$this->forgetComputed();

// Clear a single property
$this->forgetComputed($property);

// Clear multiple properties
$this->forgetComputed([$property1, $property2]);

// Clear a single computed property with arguments
$this->forgetComputedWithArgs($property, $arg1, $arg2);
```

## 组件属性

Yoyo 可以存储并更新请求中的变量不需要显式的包含输入元素。

对于一个匿名组件，可以通过使用一个逗号分隔的变量名称列表在组件根节点直接指定属性，这样允许不需要一个组件类实现一个计数：

```php
<?php $count = $count ?? 0 ; ?>
<div yoyo:props="count">
	<button yoyo:val.count="<?php echo $count + 1; ?>">+</button> 
    <p><?php echo $count; ?></p>
</div>
```

通过添加 `yoyo:props="count"`，Yoyo 知道在每一个请求里自动的包含 `count` 的值。

对于动态组件，没有必要使用 `yoyo:props` 属性，因为我们在组件中使用受保护的 $props 通过一个 变量名称的数组。

```php
class Counter extends Component
{
	public $count = 0;
	
	protected $props = ['count'];

    public function increment()
    {
        $this->count++;
    }
}
```

既然 `$count` 变量也定义为公共属性了，在模板里已经可以使用了，并且它的值通过 组件类里`increment` 方法 增加了 无需不得不 使用 `yoyo:val.count`。

```html
<div>
	<button yoyo:get="increment">+</button>
	<span><?php echo $count; ?></span>
</div>
```

## Query String

组件有能力自动更新浏览器的query  字符串当状态变化时。

```php
class Search extends Component
{
	public $query;
	
	protected $queryString = ['query'];
}
```

Yoyo 足够只能来自动移除query 字符串当当前状态值匹配了 属性的默认值。

举个例子，在一个分页组件里，你不需要 `?page=1` query 字符串出现在 URL里。

```php
class Posts extends Component
{
	public $page = 1;
	
	protected $queryString = ['page'];
}
```


# UI 组件封装方法
## 确定组件名称

## 确定组件可变化的变量（参数）固定id 为componentId

## 找出不同参数不一样的css 通过判断条件去做区分显示

## 给属性设置默认值

## 一个参数决定其他输出值用计算属性

## html 内容 用 raw 函数



