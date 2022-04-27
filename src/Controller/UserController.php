<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    /**
     * List users
     */
    public function index(): string
    {
        $userManager = new UserManager();
        $users = $userManager->selectAll();

        return $this->twig->render('User/index.html.twig', ['users' => $users]);
    }

    /**
     * Show informations for a specific user
     */
    public function show(int $id): string
    {
        $userManager = new UserManager();
        $user = $userManager->selectOneById($id);

        return $this->twig->render('User/show.html.twig', ['user' => $user]);
    }

    /**
     * Add a new user
     */
    public function add(): ?string
    {
        $userManager = new UserManager();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !empty($_POST['firstname']) &&
                !empty($_POST['lastname']) &&
                !empty($_POST['email']) &&
                !empty($_POST['password']) &&
                !empty($_POST['address']) &&
                !empty($_POST['phone'])
            ) {
                $user = array_map('trim', $_POST);
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $user = $userManager->selectOneByEmail($_POST['email']);
                    if (!$user) {
                        if (preg_match("/^([a-zA-Z-' ]*)$/", $_POST['firstname'])) {
                            if (preg_match("/^([a-zA-Z-' ]*)$/", $_POST['lastname'])) {
                                if (preg_match("#[0][1-9][- \.?]?([0-9][0-9][- \.?]?){4}$#", $_POST['phone'])) {
                                    $id = $userManager->insert([
                                        'firstname' => $_POST['firstname'],
                                        'lastname' => $_POST['lastname'],
                                        'email' => $_POST['email'],
                                        'password' => md5($_POST['password']),
                                        'address' => $_POST['address'],
                                        'phone' => $_POST['phone'],
                                    ]);
                                    header('Location:/users/show?id=' . $id);
                                    return null;
                                } else {
                                    $errors[] = "Numéro de téléphone invalide";
                                }
                            } else {
                                $errors[] = "Nom invalide";
                            }
                        } else {
                            $errors[] = "Prénom invalide";
                        }
                    } else {
                        $errors[] = "Adresse email déjà enregistrée";
                    }
                } else {
                    $errors[] = "Adresse email invalide";
                }
            } else {
                $errors[] = "Veuillez remplir tous les champs";
            }
        }
        return $this->twig->render('User/add.html.twig', ['errors' => $errors]);
    }

    /**
     * Edit a specific user
     */
    public function edit(int $id): ?string
    {
        $userManager = new UserManager();
        $user = $userManager->selectOneById($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !empty($_POST['firstname']) &&
                !empty($_POST['lastname']) &&
                !empty($_POST['email']) &&
                !empty($_POST['password']) &&
                !empty($_POST['address']) &&
                !empty($_POST['phone'])
            ) {
                $user = array_map('trim', $_POST);
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $user = $userManager->selectOneByEmail($_POST['email']);
                    if ($user['email'] === $_POST['email']) {
                        if (preg_match("/^([a-zA-Z-' ]*)$/", $_POST['firstname'])) {
                            if (preg_match("/^([a-zA-Z-' ]*)$/", $_POST['lastname'])) {
                                if (preg_match("#[0][1-9][- \.?]?([0-9][0-9][- \.?]?){4}$#", $_POST['phone'])) {
                                    $userManager->update([
                                        'id' => $id,
                                        'firstname' => $_POST['firstname'],
                                        'lastname' => $_POST['lastname'],
                                        'email' => $_POST['email'],
                                        'password' => md5($_POST['password']),
                                        'address' => $_POST['address'],
                                        'phone' => $_POST['phone'],
                                    ]);
                                    header('Location: /users/show?id=' . $id);
                                } else {
                                    $errors[] = "Numéro de téléphone invalide";
                                }
                            } else {
                                $errors[] = "Nom invalide";
                            }
                        } else {
                            $errors[] = "Prénom invalide";
                        }
                    } else {
                        $userFound = $userManager->selectOneByEmail($_POST['email']);
                        if ($userFound) {
                            $errors[] = "L'email est déjà utilisé";
                        } else {
                            if (preg_match("/^([a-zA-Z-' ]*)$/", $_POST['firstname'])) {
                                if (preg_match("/^([a-zA-Z-' ]*)$/", $_POST['lastname'])) {
                                    if (preg_match("#[0][1-9][- \.?]?([0-9][0-9][- \.?]?){4}$#", $_POST['phone'])) {
                                        $userManager->update([
                                            'firstname' => $_POST['firstname'],
                                            'lastname' => $_POST['lastname'],
                                            'email' => $_POST['email'],
                                            'password' => md5($_POST['password']),
                                            'address' => $_POST['address'],
                                            'phone' => $_POST['phone'],
                                        ]);
                                        header('Location: /users/show?id=' . $id);
                                    } else {
                                        $errors[] = "Numéro de téléphone invalide";
                                    }
                                } else {
                                    $errors[] = "Nom invalide";
                                }
                            } else {
                                $errors[] = "Prénom invalide";
                            }
                        }
                    }
                } else {
                    $errors[] = "L'adresse email est invalide";
                }
            } else {
                $errors[] = "Tous les champs sont requis";
            }
        }
        return $this->twig->render('User/edit.html.twig', ['user' => $user, 'errors' => $errors]);
    }

    /**
     * Delete a specific user
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $userManager = new UserManager();
            $userManager->delete((int)$id);

            header('Location:/users');
        }
    }
}
