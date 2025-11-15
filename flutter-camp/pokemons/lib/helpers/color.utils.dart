import 'package:flutter/material.dart';

Color getColorFromName(String? colorName) {
  switch (colorName?.toLowerCase()) {
    case 'red':
      return Colors.red;
    case 'blue':
      return Colors.blue;
    case 'yellow':
      return Colors.yellow;
    case 'green':
      return Colors.green;
    case 'purple':
      return Colors.purple;
    case 'pink':
      return Colors.pink;
    case 'brown':
      return Colors.brown;
    case 'black':
      return Colors.black;
    case 'white':
      return Colors.white;
    case 'gray':
      return Colors.grey;
    default:
      return Colors.grey;
  }
}
