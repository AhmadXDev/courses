import 'package:meals_spoon/constants/spacing.dart';
import 'package:meals_spoon/extension/nav.dart';
import 'package:meals_spoon/pages/meals_list.dart';
import 'package:meals_spoon/pages/signup_page.dart';
import 'package:meals_spoon/services/database.dart';
import 'package:meals_spoon/widgets/text_field_widget.dart';
import 'package:flutter/material.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => LoginPageState();
}

class LoginPageState extends State<LoginPage> {
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      resizeToAvoidBottomInset: false,
      appBar: AppBar(
        backgroundColor: Colors.blue.shade200,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            const Text(
              "Login",
              style: TextStyle(fontSize: 24),
            ),
            height24,
            TextFieldWidget(
              controller: emailController,
              text: "enter email",
            ),
            height24,
            TextFieldWidget(
              controller: passwordController,
              text: "enter password",
              isPassword: true,
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text("if you don't have an account"),
                TextButton(
                  onPressed: () {
                    context.pushAndRemoveUntil(const SignupPage());
                  },
                  child: const Text("signup"),
                ),
              ],
            ),
            ElevatedButton(
              onPressed: () async {
                try {
                  await Database().login(
                    email: emailController.text,
                    password: passwordController.text,
                  );
                  if (context.mounted) {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const MealsListPage(),
                        ));
                  }
                } catch (e) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(e.toString()),
                    ),
                  );
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blue.shade100,
              ),
              child: const Text(
                "Login",
                style: TextStyle(fontSize: 18),
              ),
            )
          ],
        ),
      ),
    );
  }
}
