<?php
namespace User\View\Helper;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Permissions\Acl\Acl;
use User\Model\User;
use Application\Utilities\Debug;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Helper\TranslatorAwareTrait;

class UserAwareControl extends AbstractHelper
{
	use TranslatorAwareTrait;
	public $iconPath   = '/icons/bootstrap-icons.svg#';
	public $iconHeight = '32';
	public $iconWidth  = '32';
	public $fill       = 'currentColor';
	public $iconName   = '';
	public $svgClass   = 'bi';
	public $options = [];
	public $type        = 'button';
	public $buttonClass = 'btn btn-primary';
	/**
	 * 
	 * @var \Laminas\View\Renderer\PhpRenderer;
	 */
	protected $view;
	private $iconOptionsConfigKey = 'icon_options';
	private $buttonOptionsConfigKey = 'button_options';
	/**
	 * 
	 * @var User\Model\User|User\Model\Guest $user
	 */
	protected $user;
	/**
	 * 
	 * @param User\Model\User|User\Model\Guest $user
	 * @param Acl $acl
	 */
	public function __construct($user, Acl $acl, PhpRenderer $view)
	{
		$this->user = $user;
		$this->acl = $acl;
		$this->view = $view;

		//Debug::dump($user);
	}
	public function buildControl($resource, $type = 'button', $url, array $options = [])
	{
		$translator = $this->getTranslator();
		//Debug::dump($translator);
		$html = '';
		$this->setType($type);
		if($this->type === 'button') {
			if(isset($options[$this->buttonOptionsConfigKey])) {
				$this->setOptions($options[$this->buttonOptionsConfigKey]);
				if(isset($this->options['class'])) {
					$this->buttonClass = $this->options['class'];
				}
			}
			
			// if we are building a button then build it and return early
			/*
			 * <a class="btn btn-primary" href="<?= $this->url('profile', ['action' => 'edit-profile', 'userName' => $data->userName]) ?>" role="button">Edit Profile</a>
			 */
			$html .= '<a class="'.$this->buttonClass.'"';
			$html .= 'href="'.$url.'" role="button">'. $translator->translate($this->options['link_text']) .'</a>';
			return $html;
		}
		
		/**
		 * <svg class="bi" width="32" height="32" fill="currentColor">
  				<use xlink:href="bootstrap-icons.svg#toggles"/>
		   </svg>
		 * @var string $html
		 */
		
		if(!empty($options)) {
			
		}
		$html .= '<svg class="'.$this->svgClass;
		
	}
	public function __invoke($resource, $type, $url, $options = [])
	{
		//Debug::dump($url, __CLASS__ . '::' . __LINE__);
		return $this->buildControl($resource, $type, $url, $options);
	}
	/**
	 * @return the $iconPath
	 */
	public function getIconPath() {
		return $this->iconPath;
	}

	/**
	 * @return the $iconHeight
	 */
	public function getIconHeight() {
		return $this->iconHeight;
	}

	/**
	 * @return the $iconWidth
	 */
	public function getIconWidth() {
		return $this->iconWidth;
	}

	/**
	 * @return the $fill
	 */
	public function getFill() {
		return $this->fill;
	}

	/**
	 * @return the $iconName
	 */
	public function getIconName() {
		return $this->iconName;
	}

	/**
	 * @return the $svgClass
	 */
	public function getSvgClass() {
		return $this->svgClass;
	}

	/**
	 * @return the $iconOptions
	 */
	public function getIconOptions() {
		return $this->iconOptions;
	}

	/**
	 * @param string $iconPath
	 */
	public function setIconPath($iconPath) {
		$this->iconPath = $iconPath;
	}

	/**
	 * @param string $iconHeight
	 */
	public function setIconHeight($iconHeight) {
		$this->iconHeight = $iconHeight;
	}

	/**
	 * @param string $iconWidth
	 */
	public function setIconWidth($iconWidth) {
		$this->iconWidth = $iconWidth;
	}

	/**
	 * @param string $fill
	 */
	public function setFill($fill) {
		$this->fill = $fill;
	}

	/**
	 * @param string $iconName
	 */
	public function setIconName($iconName) {
		$this->iconName = $iconName;
	}

	/**
	 * @param string $svgClass
	 */
	public function setSvgClass($svgClass) {
		$this->svgClass = $svgClass;
	}

	/**
	 * @param multitype: $iconOptions
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
	/**
	 * 
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	public function getType()
	{
		return $this->type;
	}
}