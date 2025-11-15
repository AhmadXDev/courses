import 'package:flutter/material.dart';
import 'package:meals_list/extensions/push.dart';
import 'package:meals_list/pages/categories_page.dart';

class WelcomePage extends StatelessWidget {
  const WelcomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          Positioned.fill(
            child: Image.asset(
              "assets/background.png",
              fit: BoxFit.cover,
            ),
          ),
          Align(
            alignment: Alignment.bottomCenter,
            child: ElevatedButton(
              onPressed: () {
                context.push(const CategoriesPage());
              },
              child: const Text("Get Started"),
            ),
          ),
        ],
      ),
    );
  }
}
