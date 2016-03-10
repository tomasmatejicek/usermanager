<?php

namespace Mepatek\UserManager\UI;

use Nette\Application\UI\Form,
	Nette\Object,
	Nette\Security\User,
	Nette\Security\AuthenticationException;

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
		$form = new Form();
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = [$this, 'loginFormSucceeded'];
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
			$form->addError('The username or password you entered is incorrect.');
			return false;
		}

		$this->onLoginSuccess();

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
		$form = new Form();
		$form->addText('email')
			->setRequired('Please enter your e-mail.');

		$form->addSubmit('send', 'Send instruction for change password');

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
		// not set user
		if (!$values->email) {
			$form->addError('Please enter your e-mail.');
			return false;
		}

		// if not token set, user with email not exist
		if (!($token = $this->user->getAuthenticator()->resetPasswordToken($values->email))) {
			$form->addError('E-mail does not exist.');
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
		$form = new Form();

		$form->addHidden("token", $token);

		$form->addPassword('password')
			->addRule(Form::MIN_LENGTH, 'Password length must least %d characters', $this->passwordMinLength)
			->setRequired('Please enter password');
		$form->addPassword('passwordVerify', 'Heslo pro kontrolu:')
			->setRequired('Please verify password')
			->addRule(Form::EQUAL, 'Password not same', $form['password']);
		$form->addSubmit('send', 'Change password');

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
				$values->password,
				$this->passwordMinLength,
				$this->passwordMinLevel
			)) > 0
		) {
			if ($passwordSafe == 2 or $passwordSafe == 6) {
				$form->addError("Password length must least " . $this->passwordMinLength . " characters ");
			}
			if ($passwordSafe == 4 or $passwordSafe == 6) {
				$form->addError(
					"Password is too simple, it should consist of large and small letters, numbers and special characters."
				);
			}
		}

		if (!$this->user->getAuthenticator()->changePasswordToken($values->token, $values->password)) {
			$this->onRecoveryNotChangePassword();
			$form->addError('Unable to change password (token is expire)');
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
