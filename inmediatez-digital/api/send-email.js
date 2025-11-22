import nodemailer from 'nodemailer';

export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  const { email, resultado, puntuacion, fecha } = req.body;

  if (!email || !resultado || !puntuacion) {
    return res.status(400).json({ success: false, error: 'Faltan datos requeridos' });
  }

  // Validar email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return res.status(400).json({ success: false, error: 'Email inválido' });
  }

  try {
    // Configurar el transporte con SendGrid
    const transporter = nodemailer.createTransport({
      host: 'smtp.sendgrid.net',
      port: 587,
      auth: {
        user: 'apikey',
        pass: process.env.SENDGRID_API_KEY
      }
    });

    // Enviar email
    await transporter.sendMail({
      from: 'nachobustos100@gmail.com',
      to: email,
      subject: 'Tu resultado del Test de Adicción Digital - Inmediatez Digital',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
          <h2 style="color: #8294a3;">Tu resultado del Test de Adicción Digital</h2>
          
          <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p style="font-size: 18px; font-weight: bold; color: #333;">
              ${resultado}
            </p>
            <p style="color: #666;">
              <strong>Puntuación:</strong> ${puntuacion}/30
            </p>
            <p style="color: #666;">
              <strong>Fecha:</strong> ${fecha}
            </p>
          </div>

          <p style="color: #666; font-size: 14px;">
            ¡Gracias por participar en el proyecto "Inmediatez Digital"!
          </p>

          <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

          <p style="color: #999; font-size: 12px; text-align: center;">
            © 2025 - Inmediatez Digital | Ignacio Bustos
          </p>
        </div>
      `
    });

    return res.status(200).json({ 
      success: true, 
      message: 'Email enviado exitosamente' 
    });

  } catch (error) {
    console.error('Error al enviar email:', error);
    return res.status(500).json({ 
      success: false, 
      error: 'Error al enviar el email: ' + error.message 
    });
  }
}
