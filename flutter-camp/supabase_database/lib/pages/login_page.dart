import 'package:flutter/material.dart';
import 'package:supabase_database/constants/spacing.dart';
import 'package:supabase_database/extensions/nav.dart';
import 'package:supabase_database/logic/methods.dart';
import 'package:supabase_database/pages/list_page.dart';
import 'package:supabase_database/pages/signup_page.dart';
import 'package:supabase_database/widgets/text_input.dart';

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
            TextInput(
              controller: emailController,
              labelText: "enter email",
            ),
            height24,
            TextInput(
              controller: passwordController,
              labelText: "enter password",
              isPassword: true,
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text("if you don't have an account"),
                TextButton(
                  onPressed: () {
                    context.pushOut(const SignupPage());
                  },
                  child: const Text("signup"),
                ),
              ],
            ),
            ElevatedButton(
                onPressed: () {
                  loginToPage(
                    context,
                    emailController,
                    passwordController,
                    const ListPage(),
                  );
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color.fromARGB(255, 10, 67, 180),
                ),
                child: const Text(
                  "Login",
                  style: TextStyle(fontSize: 18, color: Colors.white),
                ))
          ],
        ),
      ),
    );
  }
}
