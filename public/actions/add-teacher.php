<?php
// public/actions/add-teacher.php
require_once __DIR__ . '/../../src/controllers/AdminController.php';
(new AdminController())->ajouterEnseignant();
