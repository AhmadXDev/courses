import 'package:meals_spoon/constants/spacing.dart';
import 'package:meals_spoon/extension/nav.dart';
import 'package:meals_spoon/pages/login_page.dart';
import 'package:meals_spoon/pages/meals_list.dart';
import 'package:meals_spoon/services/database.dart';
import 'package:meals_spoon/widgets/text_field_widget.dart';
import 'package:flutter/material.dart';

class SignupPage extends StatefulWidget {
  const SignupPage({super.key});
  @override
  State<SignupPage> createState() => _SignupPageState();
}

class _SignupPageState extends State<SignupPage> {
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
      appBar: AppBar(
        backgroundColor: Colors.red.shade200,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            const Text(
              "Signup",
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
                const Text("if you have already account"),
                TextButton(
                  onPressed: () {
                    context.pushAndRemoveUntil(const LoginPage());
                  },
                  child: const Text("login"),
                ),
              ],
            ),
            ElevatedButton(
              onPressed: () async {
                try {
                  await Database().signup(
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
                "signup",
                style: TextStyle(fontSize: 18),
              ),
            )
          ],
        ),
      ),
    );
  }
}
