import 'package:flutter/material.dart';

class CustomButton extends StatelessWidget {
  final Function() onPressed;
  final String text;

  const CustomButton({
    required this.onPressed,
    required this.text,
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
      onPressed: onPressed,
      style: ElevatedButton.styleFrom(
        backgroundColor: const Color.fromARGB(255, 10, 67, 180),
      ),
      child:
          Text(text, style: const TextStyle(fontSize: 18, color: Colors.white)),
    );
  }
}
