<?php

namespace Mepatek\UserManager\UI;

use Nette\Application\UI\Form,
	Nette\Object,
	Nette\Security\User,
	Nette\Utils\Validators,
	Nette\Security\AuthenticationException,
	Nette\Localization\ITranslator;

/**
 * Class UserFormFactory
 * @package Mepatek\UserManager\UI
 */
class UserFormFactory extends Object
{
	/**
	 * Minimum password length
	 * @var integer
	 */
	public $passwordMinLength = 6;
	/**
	 * Minimum password level
	 * @var integer
	 */
	public $passwordMinLevel = 2;

	/**
	 * Translator. If set all texts are translated
	 * @var ITranslator
	 */
	public $translator = null;

	/**
	 * Event - login success
	 * @var array
	 */
	public $onLoginSuccess;
	/**
	 * Event - forgot success ($email, $token)
	 * @var array
	 */
	public $onForgotSuccess;
	/**
	 * Event - recovery success
	 * @var array
	 */
	public $onRecoverySuccess;
	/**
	 * Event - recovery not change password
	 * @var array
	 */
	public $onRecoveryNotChangePassword;
	/**
	 * If do not use translator, can change forms and error messages
	 *
	 * @var array
	 */
	public $messages = [
		"username"                    => "Username",
		"username_required"           => "Please enter your username.",
		"password"                    => "Password",
		"password_required"           => "Please enter your password.",
		"remember"                    => "Keep me signed in",
		"signInSubmit"                => "Sign in",
		"email"                       => "E-mail",
		"email_required"              => "Please enter your e-mail.",
		"forgotPasswordSubmit"        => "Send instruction for change password",
		"newPassword"                 => "New password",
		"newPassword_minLength"       => "Password length must least %d characters",
		"newPassword_required"        => "Please enter password",
		"newPasswordConfirm"          => "Confirm new password",
		"newPasswordConfirm_notSame"  => "Password not same",
		"newPasswordConfirm_required" => "Please enter password",
		"recoveryPasswordSubmit"      => "Change password",

		"err_username_or_password_incorrect" => "The username or password you entered is incorrect.",
		"err_email_not_correct"              => "Please enter your e-mail.",
		"err_email_does_not_exist"           => "E-mail does not exist.",
		"err_password_too_simple"            => "Password is too simple, it should consist of large and small letters, numbers and special characters.",
		"err_password_unable_to_change"      => "Unable to change password (token is expire)",
	];
	/** @var User (must set) */
	protected $user;

	/**
	 * Create form component for login
	 *
	 * username(text)
	 * password(password)
	 * remember(checkbox)
	 * send(submit)
	 *
	 * @return Form
	 */
	public function createLoginForm()
	{
		$form = $this->getForm();
		$form->addText('username', $this->messages['username'])
			->setRequired($this->messages['username_required']);

		$form->addPassword('password', $this->messages['password'])
			->setRequired($this->messages['password_required']);

		$form->addCheckbox('remember', $this->messages['remember']);

		$form->addSubmit('send', $this->messages['signInSubmit']);

		$form->onSuccess[] = [$this, 'loginFormSucceeded'];
		return $form;
	}

	/**
	 * Get Form object
	 * Set translator if is set
	 * @return Form
	 */
	protected function getForm()
	{
		$form = new Form();
		// set translator
		if ($this->translator) {
			$form->translator = $this->translator;
		}
		return $form;
	}

	/**
	 * onSuccess event loginForm
	 *
	 * calls events onLoginSuccess[]
	 *
	 * @param Form $form
	 * @param      $values
	 *
	 * @return bool
	 */
	public function loginFormSucceeded(Form $form, $values)
	{
		if ($values->remember) {
			$this->user->setExpiration('14 days', false);
		} else {
			$this->user->setExpiration('20 minutes', true);
		}

		try {
			$this->user->login($values->username, $values->password);
		} catch (AuthenticationException $e) {
			$form->addError($this->messages['err_username_or_password_incorrect']);
			return false;
		}

		$this->onLoginSuccess($values);

		return true;
	}

	/**
	 * Create forgot password form component
	 *
	 * email(text)
	 * send(submit)
	 *
	 * @return Form
	 */
	public function createForgotPasswordForm()
	{
		$form = $this->getForm();
		$form->addText('email', $this->messages['email'])
			->setRequired($this->messages['email_required']);

		$form->addSubmit('send', $this->messages['forgotPasswordSubmit']);

		$form->onSuccess[] = [$this, 'forgotPasswordFormSucceeded'];
		return $form;
	}

	/**
	 * onSuccess event loginForm
	 *
	 * calls events onForgotSuccess[]
	 *
	 * @param Form $form
	 * @param      $values
	 *
	 * @return bool
	 */
	public function forgotPasswordFormSucceeded(Form $form, $values)
	{
		// not set email correct
		if (!Validators::isEmail($values->email)) {
			$form->addError($this->messages['err_email_not_correct']);
			return false;
		}

		// if not token set, user with email not exist
		if (!($token = $this->user->getAuthenticator()->resetPasswordToken($values->email))) {
			$form->addError($this->messages['err_email_does_not_exist']);
			return false;
		}

		$this->onForgotSuccess($values->email, $token);

		return true;
	}

	/**
	 * Create recovery password form component
	 *
	 * token(hidden)
	 * password(password)
	 * passwordVerify(password)
	 * send(submit)
	 *
	 * @param string $token
	 *
	 * @return Form
	 */
	public function createRecoveryPasswordForm($token)
	{
		$form = $this->getForm();
		$form->addHidden("token", $token);

		$form->addPassword('newPassword', $this->messages['newPassword'])
			->addRule(Form::MIN_LENGTH, $this->messages['newPassword_minLength'], $this->passwordMinLength)
			->setRequired($this->messages['newPassword_required']);
		$form->addPassword('newPasswordConfirm', $this->messages['newPasswordConfirm'])
			->setRequired($this->messages['newPasswordConfirm_required'])
			->addRule(Form::EQUAL, $this->messages['newPasswordConfirm_notSame'], $form['newPassword']);
		$form->addSubmit('send', $this->messages['recoveryPasswordSubmit']);

		$form->onSuccess[] = [$this, 'recoveryPasswordFormSucceeded'];
		return $form;
	}

	/**
	 * onSuccess event loginForm
	 *
	 * calls events onRecoverySuccess[] and onRecoveryNotChangePassword[]
	 *
	 * @param Form $form
	 * @param      $values
	 *
	 * @return bool
	 */
	public function recoveryPasswordFormSucceeded(Form $form, $values)
	{

		if (($passwordSafe = $this->user->getAuthenticator()->isPasswordSafe(
				$values->newPassword,
				$this->passwordMinLength,
				$this->passwordMinLevel
			)) > 0
		) {
			if ($passwordSafe == 2 or $passwordSafe == 6) {
				$form->addError(sprintf($this->messages['newPassword_minLength'], $this->passwordMinLength));
			}
			if ($passwordSafe == 4 or $passwordSafe == 6) {
				$form->addError($this->messages['err_password_too_simple']);
			}
		}

		if (!$this->user->getAuthenticator()->changePasswordToken($values->token, $values->password)) {
			$this->onRecoveryNotChangePassword();
			$form->addError($this->messages['err_password_unable_to_change']);
			return false;
		}

		$this->onRecoverySuccess();
		return true;
	}

	/**
	 * getter user
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * setter user
	 *
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}
}
