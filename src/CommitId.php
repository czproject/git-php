<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	class CommitId
	{
		/** @var string */
		private $id;


		/**
		 * @param string $id
		 */
		public function __construct($id)
		{
			if (!self::isValid($id)) {
				throw new InvalidArgumentException("Invalid commit ID" . (is_string($id) ? " '$id'." : ', expected string, ' . gettype($id) . ' given.'));
			}

			$this->id = $id;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			return $this->id;
		}


		/**
		 * @return string
		 */
		public function __toString()
		{
			return $this->id;
		}


		/**
		 * @param  string $id
		 * @return bool
		 */
		public static function isValid($id)
		{
			return is_string($id) && preg_match('/^[0-9a-f]{40}$/i', $id);
		}
	}
