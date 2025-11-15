import '../constant/colors.dart';
import 'package:flutter/material.dart';

class TextWidget extends StatelessWidget {
  final String name;
  final bool isBold;
  final int num;
  const TextWidget(
      {super.key, required this.name, this.isBold = false, required this.num});

  @override
  Widget build(BuildContext context) {
    return Text(
      name,
      style: TextStyle(
        fontSize: 24,
        fontWeight: isBold ? FontWeight.bold : null,
        color: num.isEven ? primaryColor : secondaryColor,
      ),
    );
  }
}
