import 'package:flutter/material.dart';

class TextWidget extends StatelessWidget {
  final String text;
  final double size;
  const TextWidget({super.key, required this.text, required this.size});

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: TextStyle(
        fontSize: size,
      ),
    );
  }
}
