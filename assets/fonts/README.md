# Font Files for Certificate Generation

## Required Font

Please place `arial.ttf` in this directory for the certificate generator to work properly.

## How to Get Arial Font

### Option 1: From Windows
If you have access to a Windows machine, copy the font from:
```
C:\Windows\Fonts\arial.ttf
```

### Option 2: Download Free Alternative
You can use a free alternative font like **Liberation Sans** or **DejaVu Sans** which are very similar to Arial:

- **DejaVu Sans**: https://dejavu-fonts.github.io/
- Download the font and rename it to `arial.ttf`

### Option 3: Use System Font (macOS)
On macOS, you can copy Helvetica:
```bash
cp /System/Library/Fonts/Helvetica.ttc assets/fonts/arial.ttf
```

## Verification

After placing the font file, the directory structure should look like:
```
assets/
└── fonts/
    ├── arial.ttf     <- Your font file here
    └── README.md
```

## Legal Note

Make sure you have the proper license to use the font file. Arial is a proprietary font owned by Microsoft. For production use, consider using free alternatives like:
- Liberation Sans (compatible replacement for Arial)
- DejaVu Sans
- Open Sans
