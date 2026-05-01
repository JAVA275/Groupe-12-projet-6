<?php
// public/actions/login.php
require_once __DIR__ . '/../../src/controllers/AuthController.php';
(new AuthController())->login();
